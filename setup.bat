IF NOT EXIST .env (copy .env.example .env);
IF NOT EXIST Dockerfile (copy Dockerfile.example Dockerfile);
IF NOT EXIST docker-compose.yml (copy docker-compose.yml.example docker-compose.yml);
IF NOT EXIST nginx\conf.d\app.conf (copy nginx\conf.d\app.conf.example nginx\conf.d\app.conf);
IF NOT EXIST php\local.ini (copy php\local.ini.example php\local.ini);
