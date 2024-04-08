
async function cadastar_monitoramento_dengue(vform)
{
    let response = await fetch('/admin/cadastrar_boletim_gestor',{
        method:'post',
        body:vform
    })

    return response;
}

async function enviar_cadastro_monitoramento_dengue()
{
    const vform = new FormData(document.getElementById('form_monitor_dengue'));
    const response = await cadastar_monitoramento_dengue(vform);

    if (response.status !== 200) {
        message('error','Um erro inesperado aconteceu. Por favor, tente novamente');
    }

    if (response.status === 200) {
        const obj = await response.json();
        let icon = obj.data.cod === 'ok'? 'success' : 'error';

        message(icon, obj.data.msg,window.location.href);
    }
}


$("#btn_cad_monitoramento_dengue").on('click',async ()=>{
    let resposta = await confirmar('Tem certeza que deseja enviar as informações ?');


    if (!resposta) {
        message('info','Cancelado pelo usuário');
        return false;
    }else{
        enviar_cadastro_monitoramento_dengue();
    }



})

$(document).ready(()=>{
    
    $("#registro_bloqueio_gestor").remove();
    $("#monitorar_gestor").remove();
    $("#num_visitas_gestor").remove();
    $("#rotinas_gestor").remove();
    $("#monitoramento_dengue_gestor").remove();
});