window.onload = function()
{
	// capturando o evento click, para todos os elementos que possui a classe buttonPaginateAjax
	Array.from( document.querySelectorAll(".buttonPaginateAjax" ), buttonPaginateAjax =>
	buttonPaginateAjax.addEventListener('click', event =>
	{
		event.preventDefault()
		setPaginatePage( buttonPaginateAjax )
	}))

	// forçando o click da primeira página, em todos os formulários
	let totalForms = document.querySelectorAll('[name="pAjaxP"]').length
	for (i=1; i<=totalForms; i++)
	{
		//document.querySelectorAll('[name="pAjaxP"]')[(i-1)].click()
		let form = document.forms[ (i-1) ];
		getPaginate(form)
	}
}

/**
 * Execua a pesquisa ajax.
 *
 * @param 	e 	Elemento button (<<, <, >, >>)
 */
async function getPaginate(form)
{
	// parâmetros da pesquisa
	let currentPage = form.querySelector('[name="pagina"]').getAttribute('value')
	let lastPage 	= parseInt( form.querySelector('[name="ultima"]').getAttribute('value') )
	let token 		= getPaginateToken(form)

	// executando o ajax e populando seu retorno na table ou seu erro.
	let ajaxForm 	= JSON.stringify({'pagina': currentPage, 'ultima': lastPage, '_Token': token })
	let ajaxOptions = { body: ajaxForm, method: 'post', headers: {'Content-Type': 'application/json' } }
	await fetch( form.action, ajaxOptions )
	.then( res => res.json() )
	.then( res => 
	{
		if ( res.status )
		{
			setPaginateTable(form, res)
		} else
		{
			form.querySelector('[name="thead"]').innerHTML = "<tr><th>erro</th></tr>"
			form.querySelector('[name="tbody"]').innerHTML = "<tr><td class='PaginateAjaxError'>"+res.mensagem+"</td></tr>"
		}
	}).catch( error => console.error(`Error: ${error}`) )
}

/**
 * Configura a página corrente
 *
 * param 	button 		Elemento button do formulário.
 */
function setPaginatePage(button)
{
	let form 		= button.form
	let currentPage = form.querySelector('[name="pagina"]').getAttribute('value')
	let lastPage 	= parseInt( form.querySelector('[name="ultima"]').getAttribute('value') )

	if ( button.name == 'pAjaxP') currentPage = 1
	if ( button.name == 'pAjaxA') currentPage--
	if ( button.name == 'pAjaxR') currentPage++
	if ( button.name == 'pAjaxU') currentPage = lastPage

	if ( currentPage > lastPage && lastPage>0 ) currentPage = lastPage
	if ( currentPage < 1) currentPage = 1
	form.querySelector('[name="pagina"]').setAttribute('value', currentPage)

	getPaginate(form)
}

/**
 * Retorna o valores do token, caso exista.
 *
 * param 	elemento 	elemento button (<<, <, >, >>)
 */
function getPaginateToken(form)
{
	// configurando o token caso exista
	token = {}
	if ( !!form.querySelector('[name="_Token[fields]"]') )
	{
		token['fields'] = form.querySelector('[name="_Token[fields]"]').getAttribute('value')
	}
	if ( !!form.querySelector('[name="_Token[unlocked]"]') )
	{
		token['unlocked'] = form.querySelector('[name="_Token[unlocked]"]').getAttribute('value')
	}
	if ( !!form.querySelector('[name="_Token[debug]"]') )
	{
		token['debug'] = form.querySelector('[name="_Token[debug]"]').getAttribute('value')
	}

	return token
}

/**
 * Executa a ação, escolhida pelo usuário.
 *
 * param 	e 	Elemento select
 */
function setPaginateAction(elemento)
{
	let opcao = elemento.selectedIndex
	
	if ( opcao > 0)
	{
		acao = elemento.value
		if ( acao.indexOf("(") > -1 )
		{

		} else if ( acao.length > 0)
		{
			document.location.href = acao
		}
	}
}

/**
 * Escreve na tabela o resultado da pesquisa ajax
 *
 * param 	elemento 		Elemento button (<<, <, >, >>)
 * param 	res 			Resposta da pesquisas ajax.
 */
function setPaginateTable(form, res)
{
	let linkAcoes 	= '';
	if ( typeof form.querySelector('[name="acoes"]') != 'undefined' )
	{
		acoes 		= JSON.parse(form.querySelector('[name="acoes"]').getAttribute('value'))
		linkAcoes 	= "<select name='selectPaginateAjax' class='selectPaginateAjax' onclick=setPaginateAction(this)>"
		linkAcoes	+= "<option>-- Açoes --</option>"
		for ( acao in acoes )
		{
			linkAcoes += "<option value='"+acoes[acao]+"'>"+acao+"</option>"
		}
		linkAcoes  	+= "</select>"
	}

	let spanPagina 	= form.querySelector('[id="spanPagina"]')
	let spanTotal 	= form.querySelector('[id="spanTotal"]')
	let spanFaixa  	= form.querySelector('[id="spanFaixa"]')
	let spanUltima  = form.querySelector('[id="spanUltima"]')

	spanPagina.textContent 	= res.paginacao.pagina
	spanTotal.textContent 	= res.paginacao.total.toLocaleString()
	spanFaixa.textContent 	= res.paginacao.faixa
	spanUltima.textContent 	= res.paginacao.ultima.toLocaleString()
	form.querySelector('[name="ultima"]').setAttribute('value', res.paginacao.ultima)

	let linesTbody 	= ""
	let linesThead 	= ""
	let ll 			= 0
	Object.keys(res.lista).forEach( (l, linha) =>
	{
		let cloneLinkAcoes = linkAcoes
		linesTbody += "<tr>"
		Object.keys(res.lista[linha]).forEach( (cmp) =>
		{
			let vlr = res.lista[linha][cmp]
			if ( !ll ) { linesThead += "<th>"+cmp+"</th>" }
			linesTbody += "<td>"+vlr+"</td>"
			cloneLinkAcoes = cloneLinkAcoes.replace( new RegExp(`{${cmp}}`, 'g') , vlr)
		})

		if ( linkAcoes.length )
		{
			linesTbody += "<td class='tdPaginateAjaxAcao'>"+cloneLinkAcoes+"</td>"
		}

		linesTbody += "</tr>"
		ll++
	})

	if ( linkAcoes.length ) { linesThead += "<th name='thAcoes' class='thPaginateAjaxAcoes'>Ações</th>" }

	form.querySelector('[name="thead"]').innerHTML = "<tr>"+linesThead+"</tr>"
	form.querySelector('[name="tbody"]').innerHTML = linesTbody
}