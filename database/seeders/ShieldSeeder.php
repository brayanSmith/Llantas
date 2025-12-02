<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["ViewAny:BodegaResource","View:BodegaResource","Create:BodegaResource","Update:BodegaResource","Delete:BodegaResource","Restore:BodegaResource","ForceDelete:BodegaResource","ForceDeleteAny:BodegaResource","RestoreAny:BodegaResource","Replicate:BodegaResource","Reorder:BodegaResource","ViewAny:CatalogoResource","View:CatalogoResource","Create:CatalogoResource","Update:CatalogoResource","Delete:CatalogoResource","Restore:CatalogoResource","ForceDelete:CatalogoResource","ForceDeleteAny:CatalogoResource","RestoreAny:CatalogoResource","Replicate:CatalogoResource","Reorder:CatalogoResource","ViewAny:CategoriaResource","View:CategoriaResource","Create:CategoriaResource","Update:CategoriaResource","Delete:CategoriaResource","Restore:CategoriaResource","ForceDelete:CategoriaResource","ForceDeleteAny:CategoriaResource","RestoreAny:CategoriaResource","Replicate:CategoriaResource","Reorder:CategoriaResource","ViewAny:ClienteResource","View:ClienteResource","Create:ClienteResource","Update:ClienteResource","Delete:ClienteResource","Restore:ClienteResource","ForceDelete:ClienteResource","ForceDeleteAny:ClienteResource","RestoreAny:ClienteResource","Replicate:ClienteResource","Reorder:ClienteResource","ViewAny:ComprasAnuladasResource","View:ComprasAnuladasResource","Create:ComprasAnuladasResource","Update:ComprasAnuladasResource","Delete:ComprasAnuladasResource","Restore:ComprasAnuladasResource","ForceDelete:ComprasAnuladasResource","ForceDeleteAny:ComprasAnuladasResource","RestoreAny:ComprasAnuladasResource","Replicate:ComprasAnuladasResource","Reorder:ComprasAnuladasResource","ViewAny:ComprasEstadoEnCarteraResource","View:ComprasEstadoEnCarteraResource","Create:ComprasEstadoEnCarteraResource","Update:ComprasEstadoEnCarteraResource","Delete:ComprasEstadoEnCarteraResource","Restore:ComprasEstadoEnCarteraResource","ForceDelete:ComprasEstadoEnCarteraResource","ForceDeleteAny:ComprasEstadoEnCarteraResource","RestoreAny:ComprasEstadoEnCarteraResource","Replicate:ComprasEstadoEnCarteraResource","Reorder:ComprasEstadoEnCarteraResource","ViewAny:ComprasEstadoPagadoResource","View:ComprasEstadoPagadoResource","Create:ComprasEstadoPagadoResource","Update:ComprasEstadoPagadoResource","Delete:ComprasEstadoPagadoResource","Restore:ComprasEstadoPagadoResource","ForceDelete:ComprasEstadoPagadoResource","ForceDeleteAny:ComprasEstadoPagadoResource","RestoreAny:ComprasEstadoPagadoResource","Replicate:ComprasEstadoPagadoResource","Reorder:ComprasEstadoPagadoResource","ViewAny:ComprasFacturadasResource","View:ComprasFacturadasResource","Create:ComprasFacturadasResource","Update:ComprasFacturadasResource","Delete:ComprasFacturadasResource","Restore:ComprasFacturadasResource","ForceDelete:ComprasFacturadasResource","ForceDeleteAny:ComprasFacturadasResource","RestoreAny:ComprasFacturadasResource","Replicate:ComprasFacturadasResource","Reorder:ComprasFacturadasResource","ViewAny:ComprasPendientesResource","View:ComprasPendientesResource","Create:ComprasPendientesResource","Update:ComprasPendientesResource","Delete:ComprasPendientesResource","Restore:ComprasPendientesResource","ForceDelete:ComprasPendientesResource","ForceDeleteAny:ComprasPendientesResource","RestoreAny:ComprasPendientesResource","Replicate:ComprasPendientesResource","Reorder:ComprasPendientesResource","ViewAny:CompraResource","View:CompraResource","Create:CompraResource","Update:CompraResource","Delete:CompraResource","Restore:CompraResource","ForceDelete:CompraResource","ForceDeleteAny:CompraResource","RestoreAny:CompraResource","Replicate:CompraResource","Reorder:CompraResource","ViewAny:EmpresaResource","View:EmpresaResource","Create:EmpresaResource","Update:EmpresaResource","Delete:EmpresaResource","Restore:EmpresaResource","ForceDelete:EmpresaResource","ForceDeleteAny:EmpresaResource","RestoreAny:EmpresaResource","Replicate:EmpresaResource","Reorder:EmpresaResource","ViewAny:FormulaResource","View:FormulaResource","Create:FormulaResource","Update:FormulaResource","Delete:FormulaResource","Restore:FormulaResource","ForceDelete:FormulaResource","ForceDeleteAny:FormulaResource","RestoreAny:FormulaResource","Replicate:FormulaResource","Reorder:FormulaResource","ViewAny:IngredienteResource","View:IngredienteResource","Create:IngredienteResource","Update:IngredienteResource","Delete:IngredienteResource","Restore:IngredienteResource","ForceDelete:IngredienteResource","ForceDeleteAny:IngredienteResource","RestoreAny:IngredienteResource","Replicate:IngredienteResource","Reorder:IngredienteResource","ViewAny:MedidaResource","View:MedidaResource","Create:MedidaResource","Update:MedidaResource","Delete:MedidaResource","Restore:MedidaResource","ForceDelete:MedidaResource","ForceDeleteAny:MedidaResource","RestoreAny:MedidaResource","Replicate:MedidaResource","Reorder:MedidaResource","ViewAny:PedidoDomiciliarioResource","View:PedidoDomiciliarioResource","Create:PedidoDomiciliarioResource","Update:PedidoDomiciliarioResource","Delete:PedidoDomiciliarioResource","Restore:PedidoDomiciliarioResource","ForceDelete:PedidoDomiciliarioResource","ForceDeleteAny:PedidoDomiciliarioResource","RestoreAny:PedidoDomiciliarioResource","Replicate:PedidoDomiciliarioResource","Reorder:PedidoDomiciliarioResource","ViewAny:PedidosAnuladosResource","View:PedidosAnuladosResource","Create:PedidosAnuladosResource","Update:PedidosAnuladosResource","Delete:PedidosAnuladosResource","Restore:PedidosAnuladosResource","ForceDelete:PedidosAnuladosResource","ForceDeleteAny:PedidosAnuladosResource","RestoreAny:PedidosAnuladosResource","Replicate:PedidosAnuladosResource","Reorder:PedidosAnuladosResource","ViewAny:PedidosEstadoPagoEnCarteraResource","View:PedidosEstadoPagoEnCarteraResource","Create:PedidosEstadoPagoEnCarteraResource","Update:PedidosEstadoPagoEnCarteraResource","Delete:PedidosEstadoPagoEnCarteraResource","Restore:PedidosEstadoPagoEnCarteraResource","ForceDelete:PedidosEstadoPagoEnCarteraResource","ForceDeleteAny:PedidosEstadoPagoEnCarteraResource","RestoreAny:PedidosEstadoPagoEnCarteraResource","Replicate:PedidosEstadoPagoEnCarteraResource","Reorder:PedidosEstadoPagoEnCarteraResource","ViewAny:PedidosEstadoPagoSaldadoResource","View:PedidosEstadoPagoSaldadoResource","Create:PedidosEstadoPagoSaldadoResource","Update:PedidosEstadoPagoSaldadoResource","Delete:PedidosEstadoPagoSaldadoResource","Restore:PedidosEstadoPagoSaldadoResource","ForceDelete:PedidosEstadoPagoSaldadoResource","ForceDeleteAny:PedidosEstadoPagoSaldadoResource","RestoreAny:PedidosEstadoPagoSaldadoResource","Replicate:PedidosEstadoPagoSaldadoResource","Reorder:PedidosEstadoPagoSaldadoResource","ViewAny:PedidosFacturadosResource","View:PedidosFacturadosResource","Create:PedidosFacturadosResource","Update:PedidosFacturadosResource","Delete:PedidosFacturadosResource","Restore:PedidosFacturadosResource","ForceDelete:PedidosFacturadosResource","ForceDeleteAny:PedidosFacturadosResource","RestoreAny:PedidosFacturadosResource","Replicate:PedidosFacturadosResource","Reorder:PedidosFacturadosResource","ViewAny:PedidosPendientesResource","View:PedidosPendientesResource","Create:PedidosPendientesResource","Update:PedidosPendientesResource","Delete:PedidosPendientesResource","Restore:PedidosPendientesResource","ForceDelete:PedidosPendientesResource","ForceDeleteAny:PedidosPendientesResource","RestoreAny:PedidosPendientesResource","Replicate:PedidosPendientesResource","Reorder:PedidosPendientesResource","ViewAny:PedidoResource","View:PedidoResource","Create:PedidoResource","Update:PedidoResource","Delete:PedidoResource","Restore:PedidoResource","ForceDelete:PedidoResource","ForceDeleteAny:PedidoResource","RestoreAny:PedidoResource","Replicate:PedidoResource","Reorder:PedidoResource","ViewAny:ProduccionResource","View:ProduccionResource","Create:ProduccionResource","Update:ProduccionResource","Delete:ProduccionResource","Restore:ProduccionResource","ForceDelete:ProduccionResource","ForceDeleteAny:ProduccionResource","RestoreAny:ProduccionResource","Replicate:ProduccionResource","Reorder:ProduccionResource","ViewAny:ProductoResource","View:ProductoResource","Create:ProductoResource","Update:ProductoResource","Delete:ProductoResource","Restore:ProductoResource","ForceDelete:ProductoResource","ForceDeleteAny:ProductoResource","RestoreAny:ProductoResource","Replicate:ProductoResource","Reorder:ProductoResource","ViewAny:ProveedorResource","View:ProveedorResource","Create:ProveedorResource","Update:ProveedorResource","Delete:ProveedorResource","Restore:ProveedorResource","ForceDelete:ProveedorResource","ForceDeleteAny:ProveedorResource","RestoreAny:ProveedorResource","Replicate:ProveedorResource","Reorder:ProveedorResource","ViewAny:PucResource","View:PucResource","Create:PucResource","Update:PucResource","Delete:PucResource","Restore:PucResource","ForceDelete:PucResource","ForceDeleteAny:PucResource","RestoreAny:PucResource","Replicate:PucResource","Reorder:PucResource","ViewAny:RutaResource","View:RutaResource","Create:RutaResource","Update:RutaResource","Delete:RutaResource","Restore:RutaResource","ForceDelete:RutaResource","ForceDeleteAny:RutaResource","RestoreAny:RutaResource","Replicate:RutaResource","Reorder:RutaResource","ViewAny:SubCategoriaResource","View:SubCategoriaResource","Create:SubCategoriaResource","Update:SubCategoriaResource","Delete:SubCategoriaResource","Restore:SubCategoriaResource","ForceDelete:SubCategoriaResource","ForceDeleteAny:SubCategoriaResource","RestoreAny:SubCategoriaResource","Replicate:SubCategoriaResource","Reorder:SubCategoriaResource","ViewAny:UserResource","View:UserResource","Create:UserResource","Update:UserResource","Delete:UserResource","Restore:UserResource","ForceDelete:UserResource","ForceDeleteAny:UserResource","RestoreAny:UserResource","Replicate:UserResource","Reorder:UserResource","ViewAny:RoleResource","View:RoleResource","Create:RoleResource","Update:RoleResource","Delete:RoleResource","Restore:RoleResource","ForceDelete:RoleResource","ForceDeleteAny:RoleResource","RestoreAny:RoleResource","Replicate:RoleResource","Reorder:RoleResource","View:Pos"]},{"name":"Comercial","guard_name":"web","permissions":["ViewAny:PedidoDomiciliarioResource","View:PedidoDomiciliarioResource","Create:PedidoDomiciliarioResource","Update:PedidoDomiciliarioResource","Delete:PedidoDomiciliarioResource","Restore:PedidoDomiciliarioResource","ForceDelete:PedidoDomiciliarioResource","ForceDeleteAny:PedidoDomiciliarioResource","RestoreAny:PedidoDomiciliarioResource","Replicate:PedidoDomiciliarioResource","Reorder:PedidoDomiciliarioResource","View:Pos"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
