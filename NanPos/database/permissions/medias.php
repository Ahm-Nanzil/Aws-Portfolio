<?php

use App\Models\Permission;

if ( defined( 'NEXO_CREATE_PERMISSIONS' ) ) {
    $medias = Permission::firstOrNew( [ 'namespace' => 'possoft.upload.medias' ] );
    $medias->name = __( 'Upload Medias' );
    $medias->namespace = 'possoft.upload.medias';
    $medias->description = __( 'Let the user upload medias.' );
    $medias->save();

    $medias = Permission::firstOrNew( [ 'namespace' => 'possoft.see.medias' ] );
    $medias->name = __( 'See Medias' );
    $medias->namespace = 'possoft.see.medias';
    $medias->description = __( 'Let the user see medias.' );
    $medias->save();

    $medias = Permission::firstOrNew( [ 'namespace' => 'possoft.delete.medias' ] );
    $medias->name = __( 'Delete Medias' );
    $medias->namespace = 'possoft.delete.medias';
    $medias->description = __( 'Let the user delete medias.' );
    $medias->save();

    $medias = Permission::firstOrNew( [ 'namespace' => 'possoft.update.medias' ] );
    $medias->name = __( 'Update Medias' );
    $medias->namespace = 'possoft.update.medias';
    $medias->description = __( 'Let the user update uploaded medias.' );
    $medias->save();
}
