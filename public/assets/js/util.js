function message(icon, msg, route = null)
{
    Swal.fire({
        text: msg,
        icon: icon,
        timer: 32000,
        timerProgressBar: true,
        willClose: () => {
            if (route !== null) {
                window.location.href = route
            }
          }
      });
}


function pode_editar(dataref, format)
{
  let hoje = moment(moment().format('YYYY-MM-DD'));
        let data_criacao = moment(dataref,format);

        return data_criacao.isSame(hoje);
}



function myFunction() {
    var x = document.getElementById("myTopnav");
    if (x.className === "topnav") {
      x.className += " responsive";
    } else {
      x.className = "topnav";
    }
  }


function inactiveAnchors()
{
  let as = document.querySelectorAll('#myTopnav a');
  [... as].forEach(j=>{
    j.classList.remove('active')
  });
}


$(document).ready(()=>{
  inactiveAnchors();

  let as = document.querySelectorAll('#myTopnav a');
  [... as].forEach(j=>{
    if (window.location.href == j.href) {
      j.classList.add('active')
    }
  });

})


async function confirmar(texto)
{
  let response = await Swal.fire({
    title: texto,
    icon:'question',
    showCancelButton: true,
    confirmButtonText: "Sim",
    cancelButtonText: `Não`
  }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
      return true;
    } else {
      return false;
    }
  });

  return response;
}


async function listar_visitas()
{
  let response = await fetch('/users/listar_visitas');

  if (response.status !== 200) {
    let obj = {'cod':'fail','msg':'Não foi possível recuperar os dados no momento'};
    return obj;
  }else{
    let res = {'cod':'ok','msg':'Retornando dados'};
    let obj = await response.json();
    res.data = obj.data;
    return res;
  }
}


function limitar_date()
{
  let  campos = $("input[type='date']");
let hoje = moment().format('YYYY-MM-DD');
[... campos].forEach(e=>{
  e.max = hoje;
  e.value = hoje;
});

}

function avisar_ausencia_acoes_rotina()
{
  $("#div_tb_rotinas").html('<h2 class="text-center">Não há ações de rotina para exibir</h2>')
}

function editar_rotina(btn)
{
  let el = btn.target;
  console.log(`Recebendo para edição da rotina ${el.dataset.idrotina}`);
  $("#id_rotina").val(el.dataset.idrotina);
  $("#data_acao").val(moment(el.dataset.c0,'DD/MM/YYYY').format('YYYY-MM-DD'));
  $("#quantas_casas_visitadas").val(el.dataset.c1);
  $("#quantas_casas_com_criadouros").val(el.dataset.c2);
  $("#quantas_pessoas_orientadas").val(el.dataset.c3);

  if (el.dataset.c4 !== null) {
    $("#equipe").val(el.dataset.c4);
  }

}

function eliminar_campos_monitoramento()
{
  if (window.location.href.indexOf('monitoramento_dengue_view') > 0) {
    let trs = document.querySelectorAll('#tb_boletim_gestor tr');

    [... trs].forEach(e=>{
      e.children[12].remove()
    })
  }

 


}



function eliminar_campos_rotina()
{

  if (window.location.href.indexOf('users/registro_rotina')>=0) {
    return false;
  }

  let trs = document.querySelectorAll('#tb_rotinas tr');

[... trs].forEach(e=>{

  e.children[8].remove()


});


}


function montar_tabela_acoes_rotina(data)
{

  for (let i = 0; i < data.length; i++) {
    const el = data[i];
    let tr = document.createElement('tr');
    const campos = ['unidade','crs','uvis','ine','data_acao','criado_em','quantas_casas_visitadas','quantas_casas_com_criadouros','quantas_pessoas_orientadas','id_rotina'];

    campos.forEach(item => {
      const td = document.createElement('td');
      if (item != 'id_rotina') {
        td.innerHTML = el[item];
        tr.appendChild(td);
      }else{
        let btn = document.createElement('i');

        if (pode_editar(el.data_acao,'DD/MM/YYYY')) {
          
          btn.classList.add('fa-solid','fa-pencil')
          btn.dataset.idrotina = el.id;
          btn.dataset.c0 = el.data_acao;
          btn.dataset.c1 = el.quantas_casas_visitadas;
          btn.dataset.c2 = el.quantas_casas_com_criadouros;
          btn.dataset.c3 = el.quantas_pessoas_orientadas;
          btn.dataset.c4 = el.id_ine;
          btn.addEventListener('click',editar_rotina)
        }else{
          btn.classList.add('fa-solid','fa-ban')
        }

        
        td.appendChild(btn);
        tr.appendChild(td);
      }
    });

    $("#body_table_acoes_rotina").append(tr);
  }

  eliminar_campos_rotina();


  $("#tb_rotinas").removeClass('d-none');
  $("#tb_rotinas").DataTable({
    fixedHeader:true,
    fixedColumns: {
        left: 2
    },
    paging: true,
    scrollCollapse: true,
    dom: 'Bfrtip',
    buttons: [
        'excel','csv'
    ],
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
    },
    responsive:true
});
}

function distribuir_acoes_rotina(data)
{
  if (data.length === 0) {
    avisar_ausencia_acoes_rotina();
  }else{
    montar_tabela_acoes_rotina(data);
  }
}


async function buscar_rotinas()
{
  const response = await fetch('/users/listar_acoes_rotina');
  if (response.status !== 200) {
    message('error','Não foi possível listar as ações de rotina !');
  }else{
    const obj = await response.json();
    distribuir_acoes_rotina(obj.data);
  }
}

function listar_rotinas()
{
  let vtable = document.getElementById('div_tb_rotinas');
  if (vtable !== null) {
    buscar_rotinas();
  }
}


function editar_monitoramento(btn)
{
  let id_monitoramento = btn.target.dataset.idmonitoramento;
  let data_declarada = btn.target.dataset.c0;
  let total_de_casos_atendidos_com_suspeita_de_dengue = btn.target.dataset.c1;
  let total_de_casos_atendidos_confirmados_para_dengue = btn.target.dataset.c2;
  let total_de_testes_rapido_de_dengue_realizados = btn.target.dataset.c3;
  let total_de_testes_rapido_de_dengue_positivos = btn.target.dataset.c4;
  let estoque_diario_dos_testes_rapidos_de_dengue = btn.target.dataset.c5;
  let total_de_atendimentos_realizados_pela_unidade = btn.target.dataset.c6;

  $("#data_declarada").val(moment(data_declarada,'DD/MM/YYYY').format('YYYY-MM-DD'));
  $("#total_de_casos_atendidos_com_suspeita_de_dengue").val(total_de_casos_atendidos_com_suspeita_de_dengue);
  $("#total_de_casos_atendidos_confirmados_para_dengue").val(total_de_casos_atendidos_confirmados_para_dengue);
  $("#total_de_testes_rapido_de_dengue_realizados").val(total_de_testes_rapido_de_dengue_realizados);
  $("#total_de_testes_rapido_de_dengue_positivos").val(total_de_testes_rapido_de_dengue_positivos);
  $("#estoque_diario_dos_testes_rapidos_de_dengue").val(estoque_diario_dos_testes_rapidos_de_dengue);
  $("#total_de_atendimentos_realizados_pela_unidade").val(total_de_atendimentos_realizados_pela_unidade);
  $("#id_monitoramento").val(id_monitoramento)
  $("#btn_cad_monitoramento_dengue").text('Atualizar')


}

function montar_tabela_boletim_gestor(data)
{


  campos = ['unidade','user_cnes','crs','uvis','data_informada','criado_em','total_de_casos_atendidos_com_suspeita_de_dengue','total_de_casos_atendidos_confirmados_para_dengue','total_de_testes_rapido_de_dengue_realizados','total_de_testes_rapido_de_dengue_positivos','estoque_diario_dos_testes_rapidos_de_dengue','total_de_atendimentos_realizados_pela_unidade','id_monitoramento'];

  for (let i = 0; i < data.length; i++) {
    const el = data[i];
    const tr = document.createElement('tr')
    for (const item of campos) {
      let td = document.createElement('td');

      if (item != 'id_monitoramento') {
        
        td.innerText = el[item];
      }else{
        let btn = document.createElement('i');

        if (pode_editar(el.data_informada,'DD/MM/YYYY')) {
          
          btn.dataset.idmonitoramento = el.id;
          btn.dataset.c0 = el.criado_em;
          btn.dataset.c1 = el.total_de_casos_atendidos_com_suspeita_de_dengue;
          btn.dataset.c2 = el.total_de_casos_atendidos_confirmados_para_dengue;
          btn.dataset.c3 = el.total_de_testes_rapido_de_dengue_realizados;
          btn.dataset.c4 = el.total_de_testes_rapido_de_dengue_positivos;
          btn.dataset.c5 = el.estoque_diario_dos_testes_rapidos_de_dengue;
          btn.dataset.c6 = el.total_de_atendimentos_realizados_pela_unidade;
          btn.classList.add('fa-solid','fa-pencil');
          btn.addEventListener('click',editar_monitoramento)
        }else{
          btn.classList.add('fa-solid','fa-ban');
        }

        td.append(btn)
      }


      
      tr.appendChild(td);
    }
    $("#body_table_tb_boletim_gestor").append(tr);
  }

  eliminar_campos_monitoramento();

  $("#tb_boletim_gestor").DataTable({
    fixedHeader:false,
    fixedColumns: false,
    paging: true,
    scrollCollapse: true,
    dom: 'Bfrtip',
    buttons: [
        'excel','csv'
    ],
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
    },
    responsive:false
});
}

function avisar_ausencia_dados_boletim_gestor()
{
  $("#div_tb_boletim_gestor").html('<h2 class="text-center">Não há dados para exibir</h2>')
}

function distribuir_boletim_gestor(data)
{
    if (data.length === 0) {
      avisar_ausencia_dados_boletim_gestor();
    }else{
      montar_tabela_boletim_gestor(data);
    }
}


async function listar_boletim_gestor()
{
  const response = await fetch('/admin/listar_boletim_gestor');
  if (response.status !== 200) {
    message('error','Não foi possível recuperar os dados do boletim de monitoramento');
  }else{
    const obj = await response.json();
    distribuir_boletim_gestor(obj.data);
  }

}


function boletim_gestor()
{

  let href = window.location.href;

  if (href.indexOf('users')>0) {
    return false;
  }

  let tb = document.getElementById('div_tb_boletim_gestor');

  if (tb !== null) {
    listar_boletim_gestor();
  }

}



$(document).ready(()=>{
  limitar_date();
  listar_rotinas();
  boletim_gestor();


})