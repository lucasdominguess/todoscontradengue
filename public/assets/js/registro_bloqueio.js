async function cadastrar_logradouro()
{
    let vform = new FormData(document.getElementById('form_logradouros'));
    let response = await fetch('/admin/cadastrar_logradouro',{
    method:'post',
    body:vform
});

let obj = await response.json();
        return obj.data;


}





async function cadastrar_encerramento(form)
{
    let resposta = await confirmar('Este botão é para encerrar todos os bloqueios envolvendo esse SINAN, deseja encerrar?')

    if (!resposta) {
        Swal.fire({
            title: "Encerramento cancelado pelo usuário",
            icon: "info"
          });
    }else{

        const response = await fetch('/admin/cadastrar_encerramento_sinan',{
            method: 'post',
            body:form
        });
    
        let obj = {'cod': 'fail','message':'Não foi possivel encerrar o sinan'}
        if (response.status != 200) {
            return obj;
        }else{
            obj = await response.json();
            return obj.data;
        }
    }



}

async function envia_encerramento_sinan(sinan)
{
    
let r = null;
const hoje = moment().format('YYYY-MM-DD')
const { value: formValues } = await Swal.fire({
    title: "Encerramento sinan",
    showCancelButton: true,
    showCloseButton: true,
    html: `
    <form id="form_encerramento_sinan">
                <div class="form-group">
                    <label>Sinan</label>
                    <input type="text" name="sinan" class="form-control" readonly value="${sinan}" />
                  </div>
                <div class="form-group mt-3">
                  <label>Data Encerramento</label>
                  <input type="date" max="${hoje}" min="2024-01-01" name="data_fim_bloqueio" class="form-control" value="${hoje}" />
                </div>
                <div class="form-group mt-3">
                    <label class="d-block">Todos os quarteirões foram visitados ?</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" name="todos_quarteiroes_visitados" type="radio" id="todos_quarteiroes_visitados_sim" value="1">
                        <label class="form-check-label" for="todos_quarteiroes_visitados_sim">Sim</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" name="todos_quarteiroes_visitados" type="radio" id="todos_quarteiroes_visitados_nao" value="0">
                        <label class="form-check-label" for="todos_quarteiroes_visitados_nao">Não</label>
                      </div>
                  </div>
                </form>
    `,
    focusCancel: true,
    preConfirm: () => {
      return document.getElementById('form_encerramento_sinan');
    }
  });
  if (formValues) {
    r = formValues
  }

  return r;
}


async function encerra_sinan(btn)
{
    let sinan = btn.target.dataset.sinan;
    let vform = await envia_encerramento_sinan(sinan)
    if (vform === null) {
        message('error','Cancelado pelo usuário');
        return false;
    }else{
        const form = new FormData(vform);
        const res = await cadastrar_encerramento(form);
        let icon = res.cod == 'ok' ? 'success' : 'error'

        Swal.fire({
            title: res.message,
            icon: icon,
            willClose: () => {
                if (res.cod == 'ok') {
                    $("#table_data").html('');
                    listar_logradouros();
                }
              }
          });


    }
}

async function desativa_logradouro(btn)
{
    const sinan = btn.target.dataset.sinan;
    let vform = new FormData();
    vform.append('sinan', sinan);
    const response = await fetch('/admin/desativar_sinan',{
        method:'post',
        body:vform
    })

    if (response.status !== 200) {
        message('error','Não foi possível atualizar o sinan informado')
        return false;
    }else{
        let obj = await response.json()
        let icon = obj.data.cod ==='ok'?'success':'error'
        message(icon,obj.data.message)
    }
}

function editar_logradouro(btn)
{
    let el = btn.target;
    let id_logradouro = el.dataset.id;
    let sinan = el.dataset.c0;
    let quarteirao = el.dataset.c1;
    let logradouro = el.dataset.c2;

    
    $("#id_logradouro").val(id_logradouro);
    $("#sinan").val(sinan);
    $("#quarteirao").val(quarteirao);
    $("#logradouro").val(logradouro);
}


function montar_tabela(obj)
{
    
    let table = document.createElement('table');
    table.id = 'table_data_logradouro';
    table.classList.add('table','table-stripped','table-bordered')
    table.innerHTML = `
        <thead>
        <tr>
        <th>SINAN</th>
        <th>QUARTEIRÃO</th>
        <th>LOGRADOURO</th>
        <th>Qde visitas</th>
        <th>ENCERRAR</th>
        <th>Informado encerramento</th>
        <th>editar</th>
        </tr>
        </thead>
        <tbody id="body_table">
        </tbody>`

        $("#table_data").append(table);
    
    for (let i = 0; i < obj.length; i++) {
        const el = obj[i];
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${el.sinan}</td><td>${el.quarteirao}</td><td>${el.logradouro}</td><td>${el.total_visitas}</td><td></td><td>${el.unidade_informou_termino_visitacao}</td><td></td>`
        let btn = document.createElement('i');
        btn.dataset.sinan = el.sinan;
        //<i class="fa-regular fa-clock"></i>
        if (el.data_fim_bloqueio != null) {
            btn.classList.add('fa-solid','fa-check','text-success','encerra_sinan');
        }else{

            btn.classList.add('fa-regular','fa-clock','text-warning','encerra_sinan');
        }
        btn.addEventListener('click', encerra_sinan)
        tr.children[4].appendChild(btn);
        let bt_editar = document.createElement('i');
        bt_editar.dataset.id = el.id;
        bt_editar.dataset.c0 = el.sinan;
        bt_editar.dataset.c1 = el.quarteirao;
        bt_editar.dataset.c2 = el.logradouro;

        let hoje = moment(moment().format('YYYY-MM-DD'));
        let data_criacao = moment(el.dataref,'YYYY-MM-DD');

        if (Number(el.total_visitas) === 0 && data_criacao.isSame(hoje)) {
            bt_editar.addEventListener('click',editar_logradouro);
            bt_editar.classList.add('fa-solid','fa-pencil');
        }else{
            bt_editar.classList.add('fa-solid','fa-ban');
        }

        tr.children[6].appendChild(bt_editar)

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

$("#btn_cad_logradouro").on('click',async ()=>{
    let res = await cadastrar_logradouro();
    let icon = res.cod=='ok'?'success':'error'

    if (res.cod=='ok') {
        $("#table_data").html('');
        listar_logradouros();
    }


    message(icon, res.msg)
})



$(document).ready(()=>{
    $("#registro_bloqueio_gestor").addClass('d-none');
    $("#monitoramento_dengue_gestor").addClass('d-none');
    listar_logradouros();
    
})