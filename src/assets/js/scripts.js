// Create a properly formatted ISO string for the current local date-time.
function formatLocalDate(date) {
    var now = date ? new Date(date) : new Date(),
        tzo = -now.getTimezoneOffset(),
        dif = tzo >= 0 ? '+' : '-';

    function pad(num) {
        var norm = Math.abs(Math.floor(num));
        return (norm < 10 ? '0' : '') + norm;
    }
    
    return now.getFullYear()
        + '-' + pad(now.getMonth()+1)
        + '-' + pad(now.getDate())
        + 'T' + pad(now.getHours())
        + ':' + pad(now.getMinutes())
        + ':' + pad(now.getSeconds());
}

$(document).ready(function(){
    // Initialize bootstrap tooltips.
    $('[data-toggle="tooltip"]').tooltip({container: 'body'});
});
