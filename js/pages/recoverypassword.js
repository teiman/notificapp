function comprobarClave() {
    var clave1 = document.f1.nuev_pass.value;
    var clave2 = document.f1.rep_pass.value;
    
    if (document.f1.nuev_pass.value.length < 6) {
        document.getElementById('longitud').innerHTML = 'La contraseña debe tener minimo 6 caracteres';
        var no_puede = "si";
    } else {
        document.getElementById('longitud').innerHTML = '';
    };

    if (clave1 == clave2) {
        document.getElementById('clave').innerHTML = '';
    } else {
        document.getElementById('clave').innerHTML = 'Las contraseñas deben ser iguales';
        var no_puede = "si";
        error_claves_iguales = "si";
    };
    
    if (no_puede == "si") {
        return false;
    } else {
        return true;
    };

}
