<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all admin resources and their permissions
        $resources = [
            'seller',
            'user',
            'order',
            'product',
            'service',
            'service_order',
            'category',
            'review',
            'refund',
            'dispute',
            'payout',
            'wallet',
            'coupon',
            'subscription',
            'subscription_plan',
            'job_posting',
            'job_contract',
            'license',
            'blog_post',
            'blog_category',
            'blog_comment',
            'support_ticket',
            'contact_message',
            'newsletter_subscriber',
            'email_template',
            'email_campaign',
            'seller_verification',
            'escrow_transaction',
            'wallet_transaction',
            'activity_log',
            'fraud_alert',
            'api_key',
            'tax_rate',
            'currency',
            'affiliate',
            'product_bundle',
            'product_request',
            'gdpr_data_request',
            'chatbot_faq',
            'ai_chat_session',
            'live_chat',
            'referral_reward',
        ];

        $actions = ['view_any', 'view', 'create', 'edit', 'delete', 'delete_any'];

        // Create permissions for each resource
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => $action . '_' . $resource,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Create additional custom permissions
        $customPermissions = [
            'approve_seller',
            'reject_seller',
            'approve_refund',
            'reject_refund',
            'approve_payout',
            'reject_payout',
            'resolve_dispute',
            'manage_settings',
            'view_analytics',
            'manage_backups',
            'view_security_dashboard',
            'manage_commission',
            'manage_email_campaigns',
            'impersonate_user',
        ];

        foreach ($customPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $moderator = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
        $support = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'web']);

        // Super admin gets all permissions
        $superAdmin->givePermissionTo(Permission::all());

        // Admin gets most permissions except some sensitive ones
        $adminPermissions = Permission::whereNotIn('name', [
            'manage_backups',
            'impersonate_user',
            'delete_any_user',
            'delete_user',
        ])->get();
        $admin->syncPermissions($adminPermissions);

        // Moderator permissions
        $moderatorResources = ['review', 'blog_comment', 'support_ticket', 'dispute', 'fraud_alert'];
        $moderatorPermissions = [];
        foreach ($moderatorResources as $resource) {
            foreach (['view_any', 'view', 'edit'] as $action) {
                $moderatorPermissions[] = $action . '_' . $resource;
            }
        }
        $moderatorPermissions[] = 'resolve_dispute';
        $moderator->syncPermissions($moderatorPermissions);

        // Support permissions
        $supportResources = ['support_ticket', 'order', 'user', 'refund'];
        $supportPermissions = [];
        foreach ($supportResources as $resource) {
            foreach (['view_any', 'view'] as $action) {
                $supportPermissions[] = $action . '_' . $resource;
            }
        }
        $supportPermissions[] = 'edit_support_ticket';
        $supportPermissions[] = 'create_support_ticket';
        $support->syncPermissions($supportPermissions);

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Roles created: super_admin, admin, moderator, support');
        $this->command->info('Total permissions: ' . Permission::count());
    }
}
