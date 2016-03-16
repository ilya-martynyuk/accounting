composer install
npm install
bower intstall
bin/console doctrine:database:create
bin/console doctrine:schema:create
echo 'y' | bin/console doctrine:fixture:load

bin/console api:doc:dump
bin/console assetic:dump
bin/console cache:clear -e=prod