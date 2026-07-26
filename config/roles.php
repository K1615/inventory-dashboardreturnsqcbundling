<?php

// Central permission matrix for the demo role system.
// Roles: admin, manager, staff.
// Each permission key lists which roles are allowed to perform it.
// Anything not listed here is treated as "allowed for any logged-in role"
// (i.e. view-only pages, or actions every role can do like marking
// something Received or submitting a stock movement request).

return [

    'roles' => ['admin', 'manager', 'staff'],

    'role_labels' => [
        'admin' => 'Admin',
        'manager' => 'Manager',
        'staff' => 'Staff',
    ],

    'permissions' => [
        // Create PO / Submit-to-pipeline draft
        'create_po' => ['admin', 'manager'],

        // Approve / Void a pipeline order
        'approve_void_pipeline' => ['admin', 'manager'],

        // Discard an auto-draft
        'discard_draft' => ['admin', 'manager'],

        // Edit Min/Max limits, toggle Auto-Reorder
        'edit_limits' => ['admin', 'manager'],

        // Approve/Void a stock movement (Stock In/Out)
        'approve_void_movement' => ['admin', 'manager'],

        // Approve/Void a warehouse layout move request
        'approve_void_layout' => ['admin', 'manager'],

        // Configure/edit bundling presets
        'configure_bundling' => ['admin'],

        // Approve/Void a bundling (build/disassemble) request
        'approve_void_bundle' => ['admin', 'manager'],

        // Manage user accounts / roles
        'manage_users' => ['admin'],
    ],
];
