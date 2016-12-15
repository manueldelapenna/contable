# repair

Instalación

1) Clonar repositorio
2) Crear archivo app/config/parameters.yml y configurarlo

Ej:
	parameters:
		database_host: 127.0.0.1
		database_port: null
		database_name: repair
		database_user: root
		database_password: null
		mailer_transport: gmail
		mailer_host: null
		mailer_user: mail@gmail.com
		mailer_password: password
		secret: ThisTokenIsNotSoSecretChangeIt

        
        generar secret: http://nux.net/secret

3) Ejecutar composer install
4) Crear base de datos
	4.1)php bin/console doctrine:schema:update --dump-sql (para ver sentencias SQL)
	4.2)php bin/console doctrine:schema:update --force (para ejecutar sentencias SQL)

5)php bin/console assets:install
6)php bin/console cache:clear (limpiar manualmente si es necesario)

# contable
# contable
