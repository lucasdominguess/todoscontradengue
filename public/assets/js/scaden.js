function avisar_ausencia()
{
    $("#div_scaden").html('<h1 class="text-center">Não há dados para exibir</h1>');
}


function montar_tabela(obj)
{
    let tb = document.createElement('table');
    tb.id='tb_scaden';

    tb.innerHTML = `<table class="table table-striped">
    <thead>
      <tr>
        <th>Unidade</th>
        <th>CNES</th>
        <th>Nº Sinan</th>
        <th>Data Visita</th>
        <th>Data fim bloqueio</th>
        <th>Imóvel visitado</th>
        <th>Imóvel vistoriado</th>
      </tr>
    </thead>
    <tbody id="body_table_scaden">
      
    </tbody>
  </table>`;

  $("#div_scaden").append(tb);

    for (let i = 0; i < obj.length; i++) {
        const el = obj[i];
        let tr = document.createElement('tr');
        tr.innerHTML = `<td>${el.unidade}</td><td>${el.user_cnes}</td><td>${el.sinan}</td><td>${el.primeira_visita == null ? '' : el.primeira_visita}</td><td>${el.encerramento_gestor == null ? '' : el.encerramento_gestor}</td><td>${el.imovel_visitado}</td><td>${el.imovel_vistoriado}</td>`
        $("#body_table_scaden").append(tr);
    }


    $("#tb_scaden").DataTable({
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
        avisar_ausencia();
    }
}


async function listar()
{
    const response = await fetch('/admin/listar_scaden_uvis');

    if (response.status == 200) {
        const obj = await response.json()
        distribuir(obj.data)
    }else{

    }
}


$(document).ready(()=>{
    listar();
});