window.onload = function()
{
	// capturando o evento click, para todos os elementos que possui a classe buttonPaginateAjax
	Array.from( document.querySelectorAll(".buttonPaginateAjax" ), e =>
	e.addEventListener('click', event =>
	{
		event.preventDefault()
		getPaginate(e)
	}))

	// forçando o click da primeira página, em todos os formulários
	let totalForms = document.querySelectorAll('[name="pAjaxP"]').length
	for (i=1; i<=totalForms; i++)
	{
		document.querySelectorAll('[name="pAjaxP"]')[(i-1)].click()
	}
}

/**
 * Execua a pesquisa ajax.
 *
 * @param 	e 	Elemento button (<<, <, >, >>)
 */
async function getPaginate(e)
{
	let elemento 	= e.form
	let ajaxUrl 	= e.form.action
	let currentPage = elemento.querySelector('[name="pagina"]').getAttribute('value')
	let lastPage 	= parseInt( elemento.querySelector('[name="ultima"]').getAttribute('value') )
	let acoes 		= {}
	let token 		= {}

	// configurando o token caso exista
	if ( !!elemento.querySelector('[name="_Token[fields]"]') )
	{
		token['fields'] = elemento.querySelector('[name="_Token[fields]"]').getAttribute('value')
	}
	if ( !!elemento.querySelector('[name="_Token[unlocked]"]') )
	{
		token['unlocked'] = elemento.querySelector('[name="_Token[unlocked]"]').getAttribute('value')
	}
	if ( !!elemento.querySelector('[name="_Token[debug]"]') )
	{
		token['debug'] = elemento.querySelector('[name="_Token[debug]"]').getAttribute('value')
	}

	// configurando a página clicada pelo usuário
	if ( e.name == 'pAjaxP') currentPage = 1
	if ( e.name == 'pAjaxA') currentPage--
	if ( e.name == 'pAjaxR') currentPage++
	if ( e.name == 'pAjaxU') currentPage = lastPage
	if ( currentPage > lastPage && lastPage>0 ) currentPage = lastPage
	if ( currentPage < 1) currentPage = 1

	let spanPagina 	= elemento.querySelector('[id="spanPagina"]')
	let spanTotal 	= elemento.querySelector('[id="spanTotal"]')
	let spanFaixa  	= elemento.querySelector('[id="spanFaixa"]')
	let spanUltima  = elemento.querySelector('[id="spanUltima"]')

	elemento.querySelector('[name="pagina"]').setAttribute('value', currentPage)

	let ajaxForm 	= JSON.stringify({'pagina': currentPage, 'ultima': lastPage, '_Token': token })
	let ajaxOptions = { body: ajaxForm, method: 'post', headers: {'Content-Type': 'application/json' } }

	// executando o ajax e populando seu retorno na table ou seu erro.
	await fetch(ajaxUrl, ajaxOptions)
	.then( res => res.json() )
	.then( res => 
	{
		if ( res.status )
		{
			setPaginateTable(elemento, res)
		} else
		{
			elemento.querySelector('[name="thead"]').innerHTML = "<tr><th>erro</th></tr>"
			elemento.querySelector('[name="tbody"]').innerHTML = "<tr><td class='PaginateAjaxError'>"+res.mensagem+"</td></tr>"
		}
	}).catch( error => console.error(`Error: ${error}`) )
}

/**
 * Executa a ação, escolhida pelo usuário.
 *
 * param 	e 	Elemento select
 */
function setPaginateAction(e)
{
	let opcao = e.selectedIndex
	
	if ( opcao > 0)
	{
		acao = e.value
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
function setPaginateTable(elemento, res)
{
	let linkAcoes 	= '';
	if ( typeof elemento.querySelector('[name="acoes"]') != 'undefined' )
	{
		acoes 		= JSON.parse(elemento.querySelector('[name="acoes"]').getAttribute('value'))
		linkAcoes 	= "<select name='selectPaginateAjax' class='selectPaginateAjax' onclick=setPaginateAction(this)>"
		linkAcoes	+= "<option>-- Açoes --</option>"
		for ( acao in acoes )
		{
			linkAcoes += "<option value='"+acoes[acao]+"'>"+acao+"</option>"
		}
		linkAcoes  	+= "</select>"
	}

	spanPagina.textContent 	= res.paginacao.pagina
	spanTotal.textContent 	= res.paginacao.total.toLocaleString()
	spanFaixa.textContent 	= res.paginacao.faixa
	spanUltima.textContent 	= res.paginacao.ultima.toLocaleString()
	elemento.querySelector('[name="ultima"]').setAttribute('value', res.paginacao.ultima)

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

	elemento.querySelector('[name="thead"]').innerHTML = "<tr>"+linesThead+"</tr>"
	elemento.querySelector('[name="tbody"]').innerHTML = linesTbody
}