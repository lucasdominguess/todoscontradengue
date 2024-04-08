async function logar(vform)
{
    let response =  await fetch('/login',{
        method:'post',
        body:vform
    })

    if (response.status !== 200) {
        return null;
    }
    let obj = await response.json();
    return obj;
}



$("#btn_entrar").on('click', async ()=>{

    // let vcaptcha = $("#g-recaptcha-response").val();

    // if (vcaptcha =='') {
    //     message("error", 'Marque o captcha');
    //     return false;
    // }

    let vform = new FormData(document.getElementById('form_user'))
    let res = await logar(vform)
    res = res.data;
    if (res === null) {
        message('error','Não foi possível estabelecer comunicação com o servidor. Por favor, tente mais tarde');
        return false;
    }

    icon = res.cod == 'ok' ? 'success' : 'error';
    redirect = res.cod == 'ok' ? '/sender' : null

    message(icon, res.msg, redirect)

})


$(".ipt_login").on("focus",(e)=>{
    let pai = e.target.parentNode;
    let vlabel = document.querySelector(`#${pai.id} label`);
    vlabel.classList.remove('label-inactive');
        vlabel.classList.add('label-active');
});

$(".ipt_login").on("blur",(e)=>{
    let v = e.target.value;
    let pai = e.target.parentNode;
    let vlabel = document.querySelector(`#${pai.id} label`);
    if (v.trim().length == '') {
        
        vlabel.classList.remove('label-active');
        vlabel.classList.add('label-inactive');
    }
});