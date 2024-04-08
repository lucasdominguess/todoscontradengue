async function cadastrar_rotina(vform)
{
    const response = await fetch('/users/cadastrar_acao_rotina',{
        method:'post',
        body:vform
    });

    return response;
}



async function processar_cadastro_rotina()
{
    let vform = new FormData(document.getElementById('form_rotina'));
    const response = await cadastrar_rotina(vform);

    if (response.status !== 200) {
        message('error','Não foi possível efetuar a operação no momento. Por favor, tente mais tarde');
    }


    if (response.status === 200) {
        let obj = await response.json();
        let icon = obj.data.cod === 'ok' ? 'success' : 'error';
        message(icon, obj.data.msg, window.location.href)
    }
}

$("#cadastrar_rotina").on('click',async()=>{

    let resposta = await confirmar("Tem certeza que deseja cadastrar a informação ?");

    if (!resposta) {
        message('info','Cancelado pelo usuário');
        return false;
    }else{
        processar_cadastro_rotina();
    }
});


function distribuir_ines(ines)
{
    if (ines.length === 0) {
        return false;
    }
    
    [...ines].forEach(e=>{
        const opt = document.createElement('option');
        opt.value = e.id_ine;
        opt.innerText = e.ine;
        $("#equipe").append(opt)
    });
}



async function buscar_ines()
{
    const response = await fetch('/users/listar_ines');

    if (response.status != 200) {
        message('error','Não foi possível listar as equipes. Por favor, tente mais tarde')
        return null
    }else{
        const obj = await response.json();
        return obj;
    }
}

async function listar_ines()
{
    const ines = await buscar_ines();
    if (ines != null) {
        distribuir_ines(ines.data)
    }
}


$(document).ready(()=>{
    listar_ines();
    $("#monitorar_users").addClass('d-none')
    $("#num_visitas_users").addClass('d-none')
});