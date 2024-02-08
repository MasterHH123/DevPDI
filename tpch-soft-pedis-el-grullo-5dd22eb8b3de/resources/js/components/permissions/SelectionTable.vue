<template>
    <div class="row">
        <div 
            class="col-4" 
            v-for="(perms, entity) in groupedPermissions" 
            :key="entity">
            <div class="card">
                <p class="card-header">{{ entity }}</p>
                <ul class="list-unstyled p0 text-left">
                    <li v-for="perm in perms" :key="perm.id">
                        <label class="checkbox">
                            <input 
                                type="checkbox" 
                                :name="`permissions[${perm.name}]`" 
                                :checked="omitPermissionsForSelection"
                                :disabled="omitPermissionsForSelection">
                            <span class="checkmark"></span>
                            {{ perm.display_name }}
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        userPermissions: {
            type: Array,
            default(){
                return []
            }
        },
        isSuperAdmin: {
            type: Boolean,
            default(){
                return false
            }
        }
    },
    watch: {
        userPermissions(list){
            if( list.length > 0 ){
                setTimeout(()=>{
                    list.forEach(perm=>{
                        let check = document.querySelector(`[name="permissions[${perm.name}]"]`)
                        if( check)
                            check.checked = true
                    })
                }, 300)
            }
        }
    },
    created(){
        this.$store.dispatch('getPermissions')
    },
    computed: {
        omitPermissionsForSelection(){
            return this.isSuperAdmin === true
        },
        permissions(){
            return this.$store.state.permissions
        },
        permissionList(){
            return this.permissions.list
        },
        isLoading(){
            return this.permissions.loading
        },
        groupedPermissions(){
            let grouped = {}
            
            this.permissionList.forEach(p=>{
                if( !grouped[p.entity] )
                    grouped[p.entity] = [p]
                else
                    grouped[p.entity].push(p)
            })

            return grouped
        },
    }
}
</script>

<style lang="scss" scoped>
.card{
    padding-top: 3em;

    .loading{
        z-index: 1;
    }

    .card-header{
        position: absolute;
        top: 0;
        background: var(--gray-light);
        color: var(--white);
        left: 0;
        right: 0;
        margin: 0;
        border-top-left-radius: inherit;
        border-top-right-radius: inherit;
        padding: 0.5em;
        text-align: center;
        font-weight: bolder;
    }
}
</style>