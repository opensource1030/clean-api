role :app, %w{10.1.20.24}

set :stage, :production
set :deploy_to, '/home/deploy/webapps/clean'
set :branch, "master"
# set :keep_releases, 4
set :log_level, :info

#require custom config
# require './config/myconfig.rb'

set :ssh_options, {
  user: 'deploy'
}

set :tmp_dir, '/home/deploy/tmp'
set :grunt_tasks, 'deploy:production'

namespace :composer do

    desc "Running Composer Install"
    task :install do
        on roles(:app), in: :sequence, wait: 5 do
            within release_path  do
                execute "cd #{release_path} && composer install --no-dev --no-scripts --prefer-source --optimize-autoloader"
            end
        end
    end

end

namespace :deploy do

  before "deploy:updated", "deploy:set_permissions:acl"
  after :published, "ops:put_env"

  after :published, "composer:install"
  after :published, "laravel:permissions"
  #after :published, "laravel:migrate"
  after :published, "laravel:optimize"

  after :published, "ops:make_client_dirs"
  after :published, "ops:asset_compile"
  after :published, "ops:reset_app"
  after :published, "ops:asset_recompile"
  after :published, "laravel:restart_queue"

end
