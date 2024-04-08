

$(document).ready(()=>{
    let yesterday = moment().add(-1,'days').format('YYYY-MM-DD')
$("#dataref").prop('max', yesterday);



})