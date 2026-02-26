<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Single Tenant Mode
     |--------------------------------------------------------------------------
     |
     | Se impostato, il sistema opererà in modalità single-tenant.
     | Tutte le richieste verranno assegnate a questo tenant UUID.
     | Impostare SIMPLETENANT_SINGLE_TENANT_UUID nel file .env.
     |
     */
    'single_tenant_uuid' => env('SIMPLETENANT_SINGLE_TENANT_UUID', null),

    /*
     |--------------------------------------------------------------------------
     | Session Key
     |--------------------------------------------------------------------------
     |
     | La chiave usata per memorizzare i dati del tenant corrente in sessione.
     |
     */
    'session_key' => 'simpletenant_tenant',

    /*
     |--------------------------------------------------------------------------
     | Central Domains
     |--------------------------------------------------------------------------
     |
     | Domini che NON appartengono a nessun tenant.
     | Es: il dominio dell'admin panel centrale.
     |
     */
    'central_domains' => [],

    /*
     |--------------------------------------------------------------------------
     | Tenant Model
     |--------------------------------------------------------------------------
     |
     | La classe del modello Tenant. Puoi sovrascriverla con il tuo modello
     | personalizzato, purché estenda il modello base.
     |
     */
    'tenant_model' => \Rboschin\SimpleTenant\Models\Tenant::class ,

    /*
     |--------------------------------------------------------------------------
     | Domain Resolution
     |--------------------------------------------------------------------------
     |
     | Abilita o disabilita la risoluzione del tenant tramite dominio/sottodominio.
     |
     */
    'enable_domain_resolution' => true,

    /*
     |--------------------------------------------------------------------------
     | Path Resolution
     |--------------------------------------------------------------------------
     |
     | Abilita o disabilita la risoluzione del tenant tramite il primo
     | segmento del path URL. La route fallback viene registrata con
     | priorità bassa, dopo tutte le altre route dell'applicazione.
     |
     */
    'enable_path_resolution' => true,

    /*
     |--------------------------------------------------------------------------
     | Tenant UUID Column Name
     |--------------------------------------------------------------------------
     |
     | Il nome della colonna usata come foreign key nelle tabelle
     | che appartengono a un tenant.
     |
     */
    'tenant_uuid_column' => 'tenant_uuid',

    /*
     |--------------------------------------------------------------------------
     | Identification Request Key
     |--------------------------------------------------------------------------
     |
     | La chiave cercata nella richiesta (POST/GET) per identificare il tenant.
     | Es: 'tenant_path' in un form di login.
     |
     */
    'identification_request_key' => 'tenant_path',

];