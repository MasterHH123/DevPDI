import VueRouter from 'vue-router'

Vue.use(VueRouter)

import stats from '../views/Stats.vue'
import ProfileEdit from '../components/profile/Edit.vue'
import ProfileShow from '../components/profile/Show.vue'

// Proceedings
import proceedings from '../views/Proceedings.vue'
import expiringProceedings from '../views/ExpiringProceedings.vue'
import proceedingsAdd from '../components/proceedings/Add.vue'
import proceedingsEdit from '../components/proceedings/Edit.vue'
import proceedingsShow from '../components/proceedings/Show.vue'

// Proceeding Records
//import proceedingRecords from '../views/ProceedingRecords.vue'
import proceedingRecordsAdd from '../components/proceedings/records/Add.vue'
import proceedingRecordsShow from '../components/proceedings/records/Show.vue'
import proceedingRecordsEdit from '../components/proceedings/records/Edit.vue'

// Proceeding Templates
import proceedingTemplates from '../views/ProceedingTemplates.vue'
import proceedingTemplatesAdd from '../components/proceedings/templates/Add.vue'
import proceedingTemplatesEdit from '../components/proceedings/templates/Edit.vue'

// Users
import users from '../views/Users.vue'
import usersAdd from '../components/users/Add.vue'
import usersEdit from '../components/users/Edit.vue'

// Work shifts
import workShifts from '../views/WorkShifts.vue'

// Citizens
import citizens from '../views/Citizens.vue'
import citizensAdd from '../components/citizens/Add.vue'
import citizensEdit from '../components/citizens/Edit.vue'

//Locations
import locations from '../views/Locations.vue'

// App logs
import appLogs from '../views/AppLogs.vue'

// 400's pages
import error400 from '../views/400s.vue'

const vr = new VueRouter({
  routes: [
    {
      path: '/',
      name: 'stats',
      component: stats,
      meta: {
        viewTitle: 'Inicio',
        viewDescription: `Estadísticas generales`,
        permissionName: 'proceedings.stats'
      },
    },
    // Profile
    {
      path: '/mi-perfil',
      name: 'myProfile',
      component: ProfileEdit,
      meta: {
        viewTitle: 'Mi perfil',
        viewDescription: `Estatus de mi cuenta`,
      },
    },
    {
      path: '/usuarios/:id/perfil',
      name: 'profileShow',
      component: ProfileShow,
      meta: {
        viewTitle: 'Perfil de usuario',
        viewDescription: `Estatus de la cuenta`,
        permissionName: 'users.show'
      },
    },
    // Proceedings
    {
      path: '/expedientes',
      name: 'proceedings',
      component: proceedings,
      meta: {
        viewTitle: 'Expedientes',
        viewDescription: `Seguimiento y creación de capertas de investigación`,
        permissionName: 'proceedings.show'
      },
    },
    {
      path: '/expedientes/vencimiento',
      name: 'expiringProceedings',
      component: expiringProceedings,
      meta: {
          viewTitle: 'Expedientes a vencer pronto',
          viewDescription: `Seguimiento y creación de capertas de investigación`,
          permissionName: 'proceedings.show'
      }
    },
    {
      path: '/expedientes/nuevo',
      name: 'proceedingAdd',
      component: proceedingsAdd,
      meta: {
        viewTitle: 'Registrar expediente',
        viewDescription: `Nuevo expediente ciudadano`,
        permissionName: 'proceedings.store'
      },
    },
    {
      path: '/expedientes/:id/editar',
      name: 'proceedingEdit',
      component: proceedingsEdit,
      meta: {
        viewTitle: 'Editar expediente',
        viewDescription: `Editar datos de carpeta de investigación`,
        permissionName: 'proceedings.edit'
      },
    },
    {
      path: '/expedientes/:id/ver',
      name: 'proceedingsShow',
      component: proceedingsShow,
      meta: {
        viewTitle: 'Ver expediente',
        viewDescription: `Detalles de carpeta de investigación`,
        permissionName: 'proceedings.show'
      },
    },
    // Proceeding records
    {
      path: '/expedientes/:id/registros/nuevo',
      name: 'proceedingRecordsAdd',
      component: proceedingRecordsAdd,
      meta: {
        viewTitle: 'Nuevo registro',
        viewDescription: `Registro de reporte ciudadano`,
        permissionName: 'proceedings.store'
      },
    },
    {
      path: '/expedientes/:id/registros/:record_id/ver',
      name: 'proceedingRecordsShow',
      component: proceedingRecordsShow,
      meta: {
        viewTitle: 'Ver registro',
        viewDescription: `Registro de reporte ciudadano`,
        permissionName: 'proceedings.show'
      },
    },
    {
      path: '/expedientes/:id/registros/:record_id/editar',
      name: 'proceedingRecordsEdit',
      component: proceedingRecordsEdit,
      meta: {
        viewTitle: 'Editar registro',
        viewDescription: `Editar registro de reporte ciudadano`,
        permissionName: 'proceedings.edit'
      },
    },
    // Proceeding templates
    {
      path: '/expedientes/formatos',
      name: 'proceedingTemplates',
      component: proceedingTemplates,
      meta: {
        viewTitle: 'Formatos',
        viewDescription: `Listado de formatos de expedientes`,
        permissionName: 'proceeding_templates.show',
      },
    },
    {
      path: '/expedientes/formatos/nuevo',
      name: 'proceedingTemplatesAdd',
      component: proceedingTemplatesAdd,
      meta: {
        viewTitle: 'Nuevo formato',
        viewDescription: `Agregar nuevo formato de expediente`,
        permissionName: 'proceeding_templates.store',
      },
    },
    {
      path: '/expedientes/formatos/:id/editar',
      name: 'proceedingTemplatesEdit',
      component: proceedingTemplatesEdit,
      meta: {
        viewTitle: 'Editar formato',
        viewDescription: `Editar la estructura y campos del formato`,
        permissionName: 'proceeding_templates.update',
      },
    },
    // Users
    {
      path: '/usuarios',
      name: 'users',
      component: users,
      meta: {
        viewTitle: 'Usuarios',
        viewDescription: `Listado de usuarios del sistema`,
        permissionName: 'users.show',
      },
    },
    {
      path: '/usuarios/nuevo',
      name: 'usersAdd',
      component: usersAdd,
      meta: {
        viewTitle: 'Agregar usuario',
        viewDescription: `Nueva cuenta de acceso al sistema`,
        permissionName: 'users.store',
      },
    },
    {
      path: '/usuarios/:id/editar',
      name: 'usersEdit',
      component: usersEdit,
      meta: {
        viewTitle: 'Editar usuario',
        viewDescription: `Modificar los datos y permisos de la cuenta`,
        permissionName: 'users.update',
      },
    },
    // Work shifts
    {
      path: '/turnos',
      name: 'workShifts',
      component: workShifts,
      meta: {
        viewTitle: 'Turnos',
        viewDescription: `Listado de turnos de trabajo`,
        permissionName: 'users.work_shifts',
      },
    },
    // Citizens
    {
      path: '/ciudadanos',
      name: 'citizens',
      component: citizens,
      meta: {
        viewTitle: 'Ciudadanos',
        viewDescription: `Listado de ciudadanos registrados`,
        permissionName: 'citizens.show',
      },
    },
    {
      path: '/ciudadanos/nuevo',
      name: 'citizensAdd',
      component: citizensAdd,
      meta: {
        viewTitle: 'Agregar ciudadano',
        viewDescription: `Nuevo registro de ciudadano`,
        permissionName: 'citizens.store',
      },
    },
    {
      path: '/ciudadanos/:id/editar',
      name: 'citizensEdit',
      component: citizensEdit,
      meta: {
        viewTitle: 'Editar ciudadano',
        viewDescription: `Modificar los datos del ciudadano registrado`,
        permissionName: 'citizens.update',
      },
    },
      // Locations
      {
          path: '/ubicaciones',
          name: 'locations',
          component: locations,
          meta: {
              viewTitle: 'Ubicaciones',
              viewDescription: 'Ubicacion GPS del ciudadano registrado',
              permissionName: 'locations.show'
          }
      },
      // AppLogs
    {
      path: '/bitacora',
      name: 'appLogs',
      component: appLogs,
      meta: {
        viewTitle: 'Registro de actividad',
        viewDescription: `Historial de operaciones`,
        permissionName: 'app_logs.show',
      },
    },
    // 403
    {
      path: '/403',
      name: 'NotAllowed',
      component: error400,
      meta: {
        viewTitle: '403',
        viewDescription: `No tienes permiso para ver esto.`,
      },
    },
    // 404
    {
      path: '*',
      name: 'NotFound',
      component: error400,
      meta: {
        viewTitle: '404',
        viewDescription: `Página no encontrada!`,
      },
    }
  ]
})

// Permission validation for client routing
vr.beforeEach((to, from, next) => {
  let routePermissionName = to.meta.permissionName

  if( !routePermissionName )
    next()
  else if( window.userHasPermissionTo( routePermissionName) )
    next()
  else
    next({name: 'NotAllowed'}) // 403
})

export function router(){
  return vr
}
