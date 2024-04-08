function montar_tabela(obj)
{
    let tb = document.createElement('table');
    tb.id = 'tb_listagem';
    tb.innerHTML = `<table class="table table-striped">
    <thead>
      <tr>
        <th>CNES</th>
        <th>unidade</th>
        <th>uvis</th>
        <th>crs</th>
        <th>sinan</th>
        <th>quarteirao</th>
        <th>logradouro</th>
        <th>Nº logradouro</th>
        <th>complemento</th>
        <th>necessidade touca</th>
        <th>Impossível remover criadouros</th>

      </tr>
    </thead>
    <tbody id="body_table_listagem">
     
    </tbody>
  </table>`;

  $("#div_listagem").append(tb);

for (let i = 0; i < obj.length; i++) {
    const el = obj[i];

    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${el.user_cnes}</td><td>${el.unidade}</td><td>${el.uvis}</td><td>${el.crs}</td><td>${el.sinan}</td><td>${el.quarteirao}</td><td>${el.logradouro}</td><td>${el.num_logradouro == null ? '' :el.num_logradouro}</td><td>${el.complemento == null ? '' :el.complemento}</td><td>${el.necessidade_touca}</td><td>${el.impossivel_remover_criadouro}</td>`
    
    $("#body_table_listagem").append(tr);
}

$("#tb_listagem").DataTable({
    dom: 'Bfrtip',
    buttons: [
        'excel','csv'
    ],
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
    }
});


}

function distribuir(obj)
{
    if (obj.length !== 0) {
        montar_tabela(obj)
    }else{
        avisar_ausencia()
    }
}


async function listar()
{
    const response = await fetch('/admin/relatorio_listagem');

    if (response.status == 200) {
        const obj = await response.json()
        distribuir(obj.data)
    }else{

    }
}


$(document).ready(()=>{
    listar();
});