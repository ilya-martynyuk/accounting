composer install
bin/console doctrine:database:create
bin/console doctrine:schema:create
echo 'y' | bin/console doctrine:fixture:load
bin/console api:doc:dump
bin/console cache:clear -e=prod