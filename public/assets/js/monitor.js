function informar_ausencia_visitas()
{
    $("#tb_visita").html('<h2 class="text-center">Não há visitas para exibir</h2>');
}

function montar_tb_visitas(obj)
{
    let tb = document.createElement('table');
    tb.id = 'tbvisitas';
    tb.classList.add('table','table-bordered','table-striped');
    tb.innerHTML = `<thead>
<tr>
<th>Unidade</th>
<th>CNES</th>
<th>Nº Sinan</th>
<th>Quarteirão</th>
<th>Logradouro</th>
<th>Nº logradouro</th>
<th>Complemento</th>
<th>Data visita</th>
<th>Data encerramento Gestor</th>
<th>Todos quarteirões foram visitados ?</th>
<th>Imovel visitado ?</th>
<th>Imovel vistoriado ?</th>
<th>Identificado criadouros ?</th>
<th>Eliminado criadouros ?</th>
<th>Necessidade touca ?</th>
<th>Impossivel remover criadouro ?</th>
<th>Observações</th>
</tr>
    </thead>
    <tbody id="body_visitas"></tbody>
    `;


const cabecalho = ['user_name',
'cnes_unidade',
'sinan',
'quarteirao',
'logradouro',
'num_logradouro',
'complemento',
'data_visita_informada',
'data_fim_bloqueio',
'todos_quarteiroes_visitados',
'imovel_visitado',
'imovel_vistoriado',
'identificado_criadouros',
'eliminado_criadouros',
'necessidade_touca',
'impossivel_remover_criadouro',
'observacoes']

$("#tb_visita").append(tb);

for (let i = 0; i < obj.length; i++) {
    const el = obj[i];

    let tr = document.createElement('tr')
for (let index = 0; index < cabecalho.length; index++) {
    const item = cabecalho[index];
    let td = document.createElement('td');
    td.innerText = el[item] == null || el[item]==undefined ? '' : el[item];
    tr.append(td)
}

$("#body_visitas").append(tr);
    
}


$("#tbvisitas").DataTable({
    fixedHeader:true,
    fixedColumns: {
        left: 2
    },
    paging: false,
    scrollCollapse: true,
    scrollX: true,
    scrollY: 300,
    dom: 'Bfrtip',
    buttons: [
        'excel','csv'
    ],
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
    }
});

}

$(document).ready(async()=>{
    let obj = await listar_visitas();
    $("#monitoramento_dengue_gestor").remove()
    
    switch (obj.cod) {
        case 'fail':
            message('error',obj.msg);
            break;
    
        case 'ok':
            if (obj.data.length === 0) {
                informar_ausencia_visitas();
            }else{
                montar_tb_visitas(obj.data);
            }
            break;
    
        default:
            break;
    }
});