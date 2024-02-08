import Vuex from 'vuex';
import axios from 'axios';

Vue.use( Vuex );

export function stateManager(){
    return new Vuex.Store({
        state: {
            user: {},
            app: {
                isLoading: false,
                isLeftNavOpen: false,
            },
            stats: {
                loading: false,
                data: {
                    users: {},
                    citizens: {},
                    proceedings: {},
                },
                filters: {}
            },
            users: {
                loading: false,
                list: [],
                pagination: {
                    prevPageUrl: null,
                    nextPageUrl: null,
                    from: null,
                    to: null,
                    perPage: 25,
                },
                filters: {
                    term: '',
                },
            },
            workShifts: {
                loading: false,
                list: [],
                pagination: {
                    prevPageUrl: null,
                    nextPageUrl: null,
                    from: null,
                    to: null,
                    perPage: 25,
                },
                filters: {
                    term: '',
                },
            },
            citizens: {
                loading: false,
                list: [],
                pagination: {
                    prevPageUrl: null,
                    nextPageUrl: null,
                    from: null,
                    to: null,
                    perPage: 25,
                },
                filters: {
                    term: '',
                },
            },
            permissions: {
                loading: false,
                list: []
            },
            proceedings: {
                loading: false,
                list: [],
                totalFiltered: 0,
                pagination: {
                    prevPageUrl: null,
                    nextPageUrl: null,
                    from: null,
                    to: null,
                    perPage: 25,
                },
                filters: {
                    term: '',
                    citizen: null,
                    status: '',
                    minAge: 0,
                    maxAge: 100,
                },
            },
            proceedingRecords: {
                loading: false,
                list: [],
                totalFiltered: 0,
                pagination: {
                    prevPageUrl: null,
                    nextPageUrl: null,
                    from: null,
                    to: null,
                    perPage: 25,
                },
                filters: {
                    term: '',
                    citizen: null,
                    user: null,
                    template: null,
                    minAge: 0,
                    maxAge: 100,
                },
            },
            proceedingTemplates: {
                loading: false,
                list: [],
                showAsTiles: false,
                pagination: {
                    prevPageUrl: null,
                    nextPageUrl: null,
                    from: null,
                    to: null,
                    perPage: 25,
                },
                filters: {
                    term: '',
                },
            },
            appLogs: {
                loading: false,
                list: [],
                pagination: {
                    prevPageUrl: null,
                    nextPageUrl: null,
                    from: null,
                    to: null,
                    perPage: 25,
                },
                filters: {
                    term: '',
                    from: null,
                    to: null,
                },
            },
        },
        mutations: {
            setUserProfile(state, user){
                state.user = user
            },
            toggleAppLoading(state, isLoading=false){
                if( !isLoading )
                    setTimeout(()=>{
                        state.app.isLoading = isLoading
                    }, 300)
                else
                    state.app.isLoading = isLoading
            },
            openLeftNav(state, opened=false){
                state.app.isLeftNavOpen = opened
            }
        },
        actions: {
            logout(){
                confirm.fire({
                    icon: 'question',
                    title: '¿Ya te vas?',
                    confirmButtonText: 'Si, adios!'
                }).then(_r=>{
                    if(!_r.value)return;

                    axios.post('/logout')
                    .then(r=>{
                        let res = r.data
                        if( res.success )
                            location.reload()
                    }).catch(err=>{
                        window.onXHRError(err)
                    })
                })
            },
            // Stats
            getStats({commit, state}, url=null){
                state.stats.loading = true
                axios.get( url || '/stats', {
                    params: {
                        filters: state.stats.filters,
                    }
                })
                .then(r=>{
                    let res = r.data
                    if( res.success )
                        state.stats.data = res.data
                })
                .catch(err=>{
                    window.onXHRError(err)
                })
                .finally(()=>state.stats.loading = false)
            },
            // Users
            getUsers({commit, state}, url=null){
                if( state.users.list.length < 1 )
                    state.users.loading = true
                axios.get( url || '/users', {
                    params: {
                        filters: state.users.filters,
                        pagination: state.users.pagination,
                        paginate: 1,
                    }
                })
                .then(r=>{
                    let res = r.data
                    if( res.success ){
                        // 
                        res = res.data
                        // Data
                        state.users.list = res.data
                        // Pagination
                        state.users.pagination.prevPageUrl = res.prev_page_url
                        state.users.pagination.nextPageUrl = res.next_page_url
                        state.users.pagination.to = res.to
                        state.users.pagination.from = res.from
                    }
                })
                .catch(err=>{
                    window.onXHRError(err)
                })
                .finally(()=>state.users.loading = false)
            },
            getUser({commit, state}, id){
                return axios.get(`/users/${id}`)
            },
            saveUser({commit, state}, data){
                return axios.post('/users', data)
            },
            updateUser({commit, state}, user){
                return axios.post(`/users/${user.id}`, user.data)
            },
            deleteUser({commit, state}, user){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Archivar la cuenta de ${user.first_name}?`,
                    text: 'Ya no podrá ingresar a la plataforma.',
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.delete(`/users/${user.id}`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                this.dispatch('getUsers')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            restoreUser({commit, state}, user){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Desarchivar la cuenta de ${user.first_name}?`,
                    text: 'Podrá acceder de nuevo a la plataforma.',
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.patch(`/users/${user.id}/restore`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                this.dispatch('getUsers')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            // Work shifts
            getWorkShifts({commit, state}, options={}){
                if( state.workShifts.list.length < 1 )
                    state.workShifts.loading = true
                axios.get( options.url || '/work-shifts', {
                    params: {
                        filters: state.workShifts.filters,
                        pagination: state.workShifts.pagination,
                        paginate: options.paginate,
                    }
                })
                .then(r=>{
                    let res = r.data
                    if( res.success ){
                        // 
                        if( options.paginate == 0)
                            return state.workShifts.list = res.data

                        res = res.data
                        // Data
                        state.workShifts.list = res.data
                        // Pagination
                        state.workShifts.pagination.prevPageUrl = res.prev_page_url
                        state.workShifts.pagination.nextPageUrl = res.next_page_url
                        state.workShifts.pagination.to = res.to
                        state.workShifts.pagination.from = res.from
                    }
                })
                .catch(err=>{
                    window.onXHRError(err)
                })
                .finally(()=>state.workShifts.loading = false)
            },
            addWorkShift({commit, state}){
                swal.fire({
                    title: 'Escribe el nombre del turno',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Agregar',
                    inputValidator: (v) => {
                        return !v && 'Olvidaste el nombre!'
                    }
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.post(`/work-shifts`, {name: resolve.value})
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                this.dispatch('getWorkShifts', {paginate:1})
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            updateWorkShift({commit, state}, ws){
                swal.fire({
                    title: 'Cambiar el turno ' + ws.name + ' a:',
                    input: 'text',
                    inputValue: ws.name,
                    showCancelButton: true,
                    confirmButtonText: 'Cambiar',
                    inputValidator: (v) => {
                        return !v && 'Olvidaste el nombre!'
                    }
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.put(`/work-shifts/${ws.id}`, {name: resolve.value})
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                this.dispatch('getWorkShifts', {paginate:1})
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            deleteWorkShift({commit, state}, ws){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Eliminar el turno ${ws.name}?`,
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.delete(`/work-shifts/${ws.id}`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                this.dispatch('getWorkShifts', {paginate:1})
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            // Citizens
            getCitizens({commit, state}, url=null){
                if( state.citizens.list.length < 1 )
                    state.citizens.loading = true
                axios.get( url || '/citizens', {
                    params: {
                        filters: state.citizens.filters,
                        pagination: state.citizens.pagination,
                        paginate: 1,
                    }
                })
                .then(r=>{
                    let res = r.data
                    if( res.success ){
                        // 
                        res = res.data
                        // Data
                        state.citizens.list = res.data
                        // Pagination
                        state.citizens.pagination.prevPageUrl = res.prev_page_url
                        state.citizens.pagination.nextPageUrl = res.next_page_url
                        state.citizens.pagination.to = res.to
                        state.citizens.pagination.from = res.from
                    }
                })
                .catch(err=>{
                    window.onXHRError(err)
                })
                .finally(()=>state.citizens.loading = false)
            },
            getCitizen({commit, state}, options={}){
                return axios.get( options.url || `/citizens/${options.id}`, {
                    params: options.params
                })
            },
            saveCitizen({commit, state}, data){
                return axios.post('/citizens', data)
            },
            updateCitizen({commit, state}, citizen){
                return axios.post(`/citizens/${citizen.id}`, citizen.data)
            },
            deleteCitizen({commit, state}, citizen){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Archivar el registro de ${citizen.first_name} ${citizen.last_name}?`,
                    text: 'Ya no será considerado en reportes y/o estadísticas. Puedes desarchivar más adelante.',
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.delete(`/citizens/${citizen.id}`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                this.dispatch('getCitizens')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            restoreCitizen({commit, state}, citizen){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Desarchivar el registro de ${citizen.first_name}?`,
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.patch(`/citizens/${citizen.id}/restore`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                this.dispatch('getCitizens')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            // Permissions
            getPermissions({commit, state}, url=null){
                state.permissions.loading = true
                axios.get( url || '/permissions')
                .then(r=>{
                    let res = r.data
                    if( res.success )
                        state.permissions.list = res.data
                })
                .catch(err=>{
                    window.onXHRError(err)
                })
                .finally(()=>state.permissions.loading = false)
            },
            // Proceedings
            getProceedings({commit, state}, url=null){
                if( state.proceedings.list.length < 1 )
                    state.proceedings.loading = true
                axios.get( url || '/proceedings', {
                    params: {
                        filters: state.proceedings.filters,
                        pagination: state.proceedings.pagination,
                        paginate: 1,
                    }
                })
                .then(r=>{
                    let res = r.data
                    if( res.success ){
                        // 
                        res = res.data
                        // Total records
                        state.proceedings.totalFiltered = r.data.total
                        // Data
                        state.proceedings.list = res.data
                        // Pagination
                        state.proceedings.pagination.prevPageUrl = res.prev_page_url
                        state.proceedings.pagination.nextPageUrl = res.next_page_url
                        state.proceedings.pagination.to = res.to
                        state.proceedings.pagination.from = res.from
                    }
                })
                .catch(err=>{
                    window.onXHRError(err)
                })
                .finally(()=>state.proceedings.loading = false)
            },
            getProceeding({commit, state}, options={}){
                return axios.get( options.url || `/proceedings/${options.id}`, {
                    params: options.params
                })
            },
            saveProceeding({commit, state}, payload){
                let data = payload.data

                if( payload.isUpdate )
                    return axios.post(`proceedings/${data.id}`, {
                        data,
                        _method: 'put',
                    })
                else
                    return axios.post('/proceedings', data)
            },
            archiveProceeding({commit, state}, record){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Archivar el expediente #${record.id} de ${record.citizen.first_name}?`,
                    text: 'Los expedientes archivados no se contemplan para estadisticas y/o reportes. Si lo necesitas, puedes des-archivar más adelante.',
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.delete(`/proceedings/${record.id}`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                if( typeof record.callbackFn == 'function' )
                                    record.callbackFn()
                                else
                                    this.dispatch('getProceedings')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            restoreProceeding({commit, state}, record){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Desarchivar el expediente #${record.id} de ${record.citizen.first_name} ?`,
                    text: 'Será contemplado para estadísticas y reportes.',
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.patch(`/proceedings/${record.id}/restore`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                if( typeof record.callbackFn == 'function' )
                                    record.callbackFn()
                                else
                                    this.dispatch('getProceedings')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            openProceeding({commit, state}, record){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Re-abrir el expediente #${record.id} de ${record.citizen.first_name} ?`,
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.patch(`/proceedings/${record.id}/open`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                if( typeof record.callbackFn == 'function' )
                                    record.callbackFn()
                                else
                                    this.dispatch('getProceedings')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            closeProceeding({commit, state}, record){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Cerrar el expediente #${record.id} de ${record.citizen.first_name} ?`,
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.patch(`/proceedings/${record.id}/close`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                if( typeof record.callbackFn == 'function' )
                                    record.callbackFn()
                                else
                                    this.dispatch('getProceedings')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            // Proceeding Records
            getProceedingRecords({commit, state}, url=null){
                if( state.proceedingRecords.list.length < 1 )
                    state.proceedingRecords.loading = true
                axios.get( url || '/proceeding-records', {
                    params: {
                        filters: state.proceedingRecords.filters,
                        pagination: state.proceedingRecords.pagination,
                        paginate: 1,
                    }
                })
                .then(r=>{
                    let res = r.data
                    if( res.success ){
                        // 
                        res = res.data
                        // Total records
                        state.proceedingRecords.totalFiltered = r.data.total
                        // Data
                        state.proceedingRecords.list = res.data
                        // Pagination
                        state.proceedingRecords.pagination.prevPageUrl = res.prev_page_url
                        state.proceedingRecords.pagination.nextPageUrl = res.next_page_url
                        state.proceedingRecords.pagination.to = res.to
                        state.proceedingRecords.pagination.from = res.from
                    }
                })
                .catch(err=>{
                    window.onXHRError(err)
                })
                .finally(()=>state.proceedingRecords.loading = false)
            },
            getProceedingRecord({commit, state}, id){
                return axios.get(`/proceeding-records/${id}`)
            },
            saveProceedingRecord({commit, state}, payload){
                let data = payload.data

                if( payload.isUpdate )
                    return axios.post(`proceeding-records/${payload.id}`, data)
                else
                    return axios.post('/proceeding-records', data)
            },
            archiveProceedingRecord({commit, state}, record){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Archivar el registro #${record.id} de ${record.citizen.first_name}?`,
                    text: 'Los registros archivados no se contemplan para estadisticas y/o reportes. Si lo necesitas, puedes des-archivar más adelante.',
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.delete(`/proceeding-records/${record.id}`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                if( typeof record.callbackFn == 'function' )
                                    record.callbackFn()
                                else
                                    this.dispatch('getProceedingRecords')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            restoreProceedingRecord({commit, state}, record){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Desarchivar el registro #${record.id} de ${record.citizen.first_name} ?`,
                    text: 'Será contemplado para estadísticas y reportes.',
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.patch(`/proceeding-records/${record.id}/restore`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                if( typeof record.callbackFn == 'function' )
                                    record.callbackFn()
                                else
                                    this.dispatch('getProceedingRecords')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            // Proceeding Templates
            getProceedingTemplates({commit, state}, options={}){
                if( state.proceedingTemplates.list.length < 1 )
                    state.proceedingTemplates.loading = true
                axios.get( options.url || '/proceeding-templates', {
                    params: {
                        filters: state.proceedingTemplates.filters,
                        pagination: state.proceedingTemplates.pagination,
                        paginate: options.paginate !== undefined ? options.paginate : 1,
                        onlyActive: options.onlyActive !== undefined ? options.onlyActive : 0
                    }
                })
                .then(r=>{
                    let res = r.data
                    if( res.success ){
                        // 
                        if( options.paginate === 0 )
                            return state.proceedingTemplates.list = res.data
                            
                        res = res.data
                        // Data
                        state.proceedingTemplates.list = res.data
                        // Pagination
                        state.proceedingTemplates.pagination.prevPageUrl = res.prev_page_url
                        state.proceedingTemplates.pagination.nextPageUrl = res.next_page_url
                        state.proceedingTemplates.pagination.to = res.to
                        state.proceedingTemplates.pagination.from = res.from
                    }
                })
                .catch(err=>{
                    window.onXHRError(err)
                })
                .finally(()=>state.proceedingTemplates.loading = false)
            },
            getProceedingTemplate({commit, state}, id){
                return axios.get(`/proceeding-templates/${id}`)
            },
            saveProceedingTemplate({commit, state}, payload){
                let data = payload.data

                if( payload.isUpdate )
                    return axios.post(`proceeding-templates/${data.id}`, {
                        data,
                        _method: 'put',
                    })
                else
                    return axios.post('/proceeding-templates', {data})
            },
            disableProceedingTemplate({commit, state}, template){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Desactivar el formato "${template.name}" ?`,
                    text: 'Ya no podrá usarse hasta que se vuelva a activar',
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.delete(`/proceeding-templates/${template.id}`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                this.dispatch('getProceedingTemplates')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            enableProceedingTemplate({commit, state}, template){
                confirm.fire({
                    icon: 'warning',
                    title: `¿Activar el formato "${template.name}" ?`,
                    text: 'Podrá utilizarse de nuevo',
                    confirmButtonText: 'Si, continuar'
                })
                .then(resolve=>{
                    if( resolve.isConfirmed )
                        axios.patch(`/proceeding-templates/${template.id}/restore`)
                        .then(res=>{
                            let response = res.data
                            if( response.success ){
                                toast.fire(response.message, '', 'success')
                                this.dispatch('getProceedingTemplates')
                            }
                        })
                        .catch(err=>{
                            window.onXHRError(err)
                        })
                })
            },
            // App Logs
            getAppLogs({commit, state}, options={}){
                if( state.appLogs.list.length < 1 )
                    state.appLogs.loading = true
                axios.get( options.url || '/app-logs', {
                    params: {
                        filters: state.appLogs.filters,
                        pagination: state.appLogs.pagination,
                        paginate: options.paginate !== undefined ? options.paginate : 1,
                    }
                })
                .then(r=>{
                    let res = r.data
                    if( res.success ){
                        // 
                        if( options.paginate === 0 )
                            return state.appLogs.list = res.data
                            
                        res = res.data
                        // Data
                        state.appLogs.list = res.data
                        // Pagination
                        state.appLogs.pagination.prevPageUrl = res.prev_page_url
                        state.appLogs.pagination.nextPageUrl = res.next_page_url
                        state.appLogs.pagination.to = res.to
                        state.appLogs.pagination.from = res.from
                    }
                })
                .catch(err=>{
                    window.onXHRError(err)
                })
                .finally(()=>state.appLogs.loading = false)
            },
        }
    })      
}
