async function cadastrar_visita()
{
    let vform = new FormData(document.getElementById('form_visitas'));
    const response = await fetch('/users/cadastrar_visita',{
        method:'post',
        body:vform
    });
    return response;
}

function liberar_form()
{
    recolher('imovel_visitado_child');
    $("#imovel_visitado_sim").prop('checked',false)
    $("#imovel_visitado_nao").prop('checked',false)
    $("#num_logradouro").val('')
    $("#complemento_logradouro").val('')
}

async function processar_cadastro_visita()
{
    let response = await cadastrar_visita();

    let status = response.status;

    if (status != 200) {
        message('error','Não foi possível processar sua solicitação no momento. Por favor, tenta mais tarde.')
    }

    if (status === 200) {
        let obj = await response.json();
        let data = obj.data;

        let icon = data.cod == 'ok' ? 'success' : 'error'

        if (icon == 'success') {
            liberar_form()
        }


        message(icon, data.msg);
    }
}

$("#btn_cadastrar_visita").on('click',async ()=>{

    let confirmacao = await confirmar('Tem certeza ?');

    if (!confirmacao) {
        message('info','Envio cancelado pelo usuário.');
        return false;
    }

    if (confirmacao) {
        processar_cadastro_visita();
    }


})



function expandir_recurse(vclass)
{
    let vdiv = document.querySelectorAll(`.${vclass}`);
    [... vdiv].forEach(e=>{
        e.classList.remove('d-none');
        e.classList.remove('fadeOut');
        e.classList.add('slideDown');
        e.classList.add('d-block');
    })
}


function expandir(vclass)
{
    
    if (vclass == 'identificado_criadouros_child') {
        expandir_recurse(vclass);
        return false;
    }
    
let vdiv = document.querySelector(`.${vclass}`);
vdiv.classList.remove('d-none');
vdiv.classList.remove('fadeOut');
vdiv.classList.add('slideDown');
vdiv.classList.add('d-block');
    

    


}

function reset_values(el)
{
    let campos = el.querySelectorAll('input');
    [... campos].forEach(e=>{
        switch (e.type) {
            case 'radio':
                e.checked = false;
                break;
        
            case 'checkbox':
                e.checked = false;
                break;
        
            case 'text':
                e.value = '';
                break;
        
            case 'date':
                e.value = '';
                break;
        
            case 'number':
                e.value = '';
                break;
        
            default:
                break;
        }
    })
}


function recolher(vclass)
{
    let vdivs = document.querySelectorAll(`.${vclass}`);

    [... vdivs].forEach(e=>{
        e.classList.remove('slideDown');
        e.classList.add('fadeOut');
        setTimeout(() => {
            e.classList.remove('d-block')
            e.classList.add('d-none');
        }, 1000);
        reset_values(e)
    });


    
 
    
}


function fnExpand(c)
{
    let escolha = c.id.substr(-3,c.id.length).toLowerCase();

    
    switch (escolha) {
        case 'sim':
            expandir(`${c.name}_child`)
            break;
        case 'nao':
            recolher(`${c.name}_child`)
            break
        default:
            break;
    }
}

function reset_modal()
{
    let c = $("#myModal input");

    [... c].forEach(e=>{
        let vtype = e.type;

        switch (vtype) {
            case 'text':
                e.value = ''
                break;
            case 'number':
                e.value = ''
                break;
                case 'date':
                    e.value = ''
                    break;
            case 'textarea':
                e.value = ''
                break;
            case 'radio':
                e.checked = false
                break;
        
            default:
                break;
        }
    });

    let els = $(".reset");
    [... els].forEach(el=>{
        el.classList.remove('d-block');
        el.classList.add('d-none');
    });
}

async function adicionar_visita(btn)
{
    let sinan = btn.target.dataset.sinan;
    let idlogradouro = btn.target.dataset.idlogradouro;
    let logradouro = btn.target.dataset.logradouro;
    let quarteirao = btn.target.dataset.quarteirao;
    reset_modal();
    $("#myModal").modal({backdrop:false, keyboard:true})
    $("#idlogradouro").val(idlogradouro);
    $("#quarteirao").val(quarteirao);
    $("#sinan").val(sinan);
    $("#logradouro").val(logradouro);
    let hoje = moment().format('YYYY-MM-DD');
    $("#dataref").val(hoje);


}


async function informa_encerramento_visita(sinan, quarteirao,logradouro)
{

    let vform = new FormData();
    vform.append('sinan', sinan);
    vform.append('quarteirao', quarteirao);
    vform.append('idlogradouro', logradouro);

    let response = await fetch('/users/informa_encerramento_visitacao',{
        method:'post',
        body:vform
    });

    if (response.status !== 200) {
        message('error','Não foi possível proceder ao solicitado no momento. Por favor, tente mais tarde.');
        return false;
    }

    let obj = await response.json();
    let data = obj.data;

    let icon = data.cod == 'ok'? 'success' : 'error'

    if (data.cod == 'ok') {
        message(icon, data.msg, window.location.href);    
    }else{

        message(icon, data.msg);
    }





}


async function confirma_encerramento_visita(btn)
{
    let sinan = btn.target.dataset.sinan
    let quarteirao = btn.target.dataset.quarteirao
    let idlogradouro = btn.target.dataset.idlogradouro
    
    let confirm = await confirmar('Você terminou as visitas desse trecho da rua desse quarteirão? Se sim, essa linha será encerrada');

    if (!confirm) {
        message('info','Encerramento cancelado pelo usuário');
        return false;
    }else{
        informa_encerramento_visita(sinan, quarteirao,idlogradouro);
    }



}

function montar_tabela(obj)
{
    
    let table = document.createElement('table');
    table.id = 'table_data_logradouro';
    table.classList.add('table','table-stripped','table-bordered')
    table.innerHTML = `
        <thead>
        <tr>
        <th>Sinan</th>
        <th>Quarteirão</th>
        <th>Logradouro</th>
        <th>Registrar visita</th>
        <th>FINALIZAR LOGRADOURO(RUA)/QUARTEIRÃO</th>
        </tr>
        </thead>
        <tbody id="body_table">
        </tbody>`

        $("#table_data").append(table);
    
    for (let i = 0; i < obj.length; i++) {
        const el = obj[i];
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${el.sinan}</td><td>${el.quarteirao}</td><td>${el.logradouro}</td><td></td><td></td>`
        
        //btn registro de visitação
        let btn = document.createElement('i');
        btn.dataset.sinan = el.sinan;
        btn.dataset.idlogradouro = el.id;
        btn.dataset.logradouro = el.logradouro;
        btn.dataset.quarteirao = el.quarteirao;
        btn.classList.add('fa-solid','fa-list-check','text-success','adicionar_visita');
 
        btn.addEventListener('click', adicionar_visita)
        tr.children[3].appendChild(btn)

        // btn encerramento visitação

        btn = document.createElement('i');
        btn.dataset.sinan = el.sinan;
        btn.dataset.quarteirao = el.quarteirao;
        btn.dataset.idlogradouro = el.id;
        let vclass = el.unidade_informou_termino_visitacao == '1' ? ['fa-solid','fa-check','text-success'] : ['fa-regular','fa-clock','text-warning'];
        
        for (let i = 0; i < vclass.length; i++) {
            const cls = vclass[i];
            btn.classList.add(cls);
        }
 
        btn.addEventListener('click', confirma_encerramento_visita)
        tr.children[4].appendChild(btn)


        $("#body_table").append(tr)
        
    }

    $("#table_data").addClass('border_table')

    $("#table_data_logradouro").DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
        },
        responsive: true
    })
}

function informar_ausencia()
{
    let h1 = document.createElement('h1');
    h1.innerText = 'Não há dados para exibir';
    h1.classList.add('text-center');
    $("#table_data").append(h1);
}

async function listar_logradouros()
{
    let response = await fetch('/users/listar_logradouros');

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
    listar_logradouros();
    
    
})
