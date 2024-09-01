window.__notificationAudio = new Audio('/sounds/noty.ogg');

window.toast = swal.mixin({
    toast: true,
    position: 'bottom-end',
    timer: 3000,
    showConfirmButton: false,
    didOpen(){
        window.playNotificationSound()
    }
})

window.swal = swal.mixin({
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Ok',
    showClass: {
        popup: '',
        icon: ''
    },
    hideClass: {
        popup: '',
    },
    didOpen(){
        window.playNotificationSound()
    }
})

window.confirm = swal.mixin({
    confirmButtonText: 'Ok',
    icon: 'question',
    showCancelButton: true,
})

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Helper functions
window.playNotificationSound = ()=>{
    if( window.__notificationAudio)
        return window.__notificationAudio.play();
    return null
}

window.getRandString = ()=>{
    return Math.random().toString(36).substr(2);
}

window.formDataToJson = (formData)=>{
    let object = {};
    formData.forEach((value, key) => object[key] = value);
    return object
}

window.onXHRError = (err, message=null)=>{
    let res = err.response
    let errors = ""

    if( !res )return swal.fire('Error desconocido', err.toString(), 'error');

    console.warn(res, res.status, res.data )

    // Unauthenticated
    if( ([401, 403, 419].indexOf(res.status) !== -1) 
        && 
        res.data &&
        res.data.message &&
        (   res.data.message.toLowerCase().indexOf('unauthenticated') !== -1
            ||    
            res.data.message.toLowerCase().indexOf('csrf') !== -1
        ) 
    ){
        // Expired session
        return authPrompt(err, message)
    }

    // 400's requests
    if( [400, 404].indexOf(res.status) !== -1 && res.data ){
        if( window.app && window.app.$router)
            window.app.$router.push('/')

        return swal.fire( (res.data.message || message), '', 'error')
    }

    // Request validation errors
    for(let e in res.data.errors){
        errors += `${res.data.errors[e][0]} ` 
    }

    swal.fire('Error', (errors || message || 'Hubo un problema desconocido'), 'error')
}

window.authPrompt = (err, message)=>{
    swal.fire({
        icon: 'info',
        title: '😴 Tu sesión ha expirado',
        text: 'Ingresa tu clave para continuar',
        input: 'password',
        inputAttributes: {
            autocomplete: 'new-password',
        },
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Salir',
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        inputValidator(v){
            return !v && 'Olvidaste tu clave' 
        },
        preConfirm: (pass) => {
            return axios.post(window.__authPath || '/authenticate', {
                email: window.__user.email,
                password: pass,
            })
            .then(r=>{
                let res = r.data
                if( res.success )
                    // Login success
                    return res;
            })
            .catch(_err=>{
                console.dir(_err)
                swal.showValidationMessage(
                    `Error: ${_err.response.data.message}`
                )
            })
        },
    }).then((_r)=>{
        // Reload on dismiss
        if( _r.isDismissed )
            return location.reload(); 

        let response = _r.value

        if( response.success ){
            toast.fire(response.message, '', 'success')
            
            // Attempt to reboot root vue component to trigger
            // posible remaining ajax calls
            let app = window.app
            if( app && app.$root && app.$root.rebootRoot )
                app.$root.rebootRoot()
        }
    })
}

window.isValidHttpUrl = (string)=>{
    let url;
    
    try {
      url = new URL(string);
    } catch (_) {
      return false;  
    }
  
    return url.protocol === "http:" || url.protocol === "https:";
}

window.getQueryParameterByName = (name, url = window.location.href)=>{
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

window.debounce = (fn, time=300)=>{
    if( window.__timeOut )
        clearTimeout( window.__timeOut )

    window.__timeOut = setTimeout(()=>{
        fn()
    }, time)
}

window.userHasPermissionTo = (permToCheck, user=null)=>{
    let _usr = user || window.__user || {}

    if( !_usr || !_usr.permissions )
        return console.warn('User instance missing!')

    let perms = _usr.permissions
    return perms.find(p=>p.name==permToCheck) !== undefined || _usr.is_admin === true
}