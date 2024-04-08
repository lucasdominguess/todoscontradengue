function montar_tabela(obj)
{
    
    let table = document.createElement('table');
    table.id = 'table_data_logradouro';
    table.classList.add('table','table-stripped','table-bordered')
    table.innerHTML = `
        <thead>
        <tr>
        <td>CNES</td>
        <td>UNIDADE</td>
        <td>CRS</td>
        <td>STS</td>
        <td>UVIS</td>
        <th>SINAN</th>
        <th>QUARTEIRÃO</th>
        <th>LOGRADOURO</th>
        <th>NÚMERO DE VISITAS</th>
        </tr>
        </thead>
        <tbody id="body_table">
        </tbody>`

        $("#table_data").append(table);
    
    for (let i = 0; i < obj.length; i++) {
        const el = obj[i];
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${el.cnes}</td><td>${el.unidade}</td><td>${el.crs}</td><td>${el.sts}</td><td>${el.uvis}</td><td>${el.sinan}</td><td>${el.quarteirao}</td><td>${el.logradouro}</td><td>${el.total_visitas}</td>`
        

        $("#body_table").append(tr)
        
    }

    $("#table_data").addClass('border_table')

    $("#table_data_logradouro").DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
        },
        responsive: true,
        dom: 'Bfrtip',
    buttons: [
        'excel','csv'
    ]
    })
}

function informar_ausencia()
{
    let h1 = document.createElement('h1');
    h1.innerText = 'Não há dados para exibir';
    h1.classList.add('text-center');
    $("#table_data").append(h1);
}



async function listar_visitas()
{
    let response = await fetch('/users/listar_consolidado_visitas');

    switch (response.status) {
        case 200:
            let obj = await response.json();
            montar_tabela(obj.data)
            break;
        case 204:
        informar_ausencia()
        break;
    
        default:
            message('error','Não foi possível efetuar a consulta no momento. Por favor, tente mais tarde')
    }
}



$(document).ready(()=>{
    $("#monitoramento_dengue_gestor").remove()
    listar_visitas();
    
})