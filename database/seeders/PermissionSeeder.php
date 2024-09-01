<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list = [
            'Expedientes' => [
                // Records
                'proceedings.show'      => 'Ver',
                'proceedings.print'     => 'Imprimir',
                'proceedings.store'     => 'Registrar',
                'proceedings.edit'      => 'Editar',
                'proceedings.archive'   => 'Archivar',
                'proceedings.stats'     => 'Ver estadísticas',
                'proceedings.reports'   => 'Exportar reportes',
            ],
            'Formatos de expedientes'  => [
                // Templates
                'proceeding_templates.show'     => 'Ver',
                'proceeding_templates.store'    => 'Agregar',
                'proceeding_templates.update'     => 'Editar',
                'proceeding_templates.archive'  => 'Archivar',
            ],
            'Registros Ciudadanos'  => [
                'citizens.show'    => 'Ver',
                'citizens.store'   => 'Registrar',
                'citizens.update'  => 'Actualizar',
                'citizens.archive' => 'Archivar',
            ],
            'Cuentas de usuarios'  => [
                'users.show'        => 'Ver',
                'users.store'       => 'Crear cuentas',
                'users.update'      => 'Actualizar cuentas',
                'users.archive'     => 'Archivar cuentas',
                'users.work_shifts' => 'Gestionar turnos',
            ],
            ['Ubicaciones de ciudadanos' => [
                'locations.show' => 'Ver',
                'locations.store' => 'Registrar'
                ]
            ],
            'Regristro de actividad' => [
                'app_logs.show'    => 'Ver registro (para apoyo técnico)',
            ]
        ];

        foreach($list as $entity => $permissions)
        {
            foreach( $permissions as $name => $display)
            {
                Permission::create([
                    'name'          => $name,
                    'display_name'  => $display,
                    'entity'        => $entity,
                    'guard_name'    => 'web',
                ]);
            }
        }
    }
}
