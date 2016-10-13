role :app, %w{204.156.175.45}

set :stage, :staging
set :deploy_to, '/home/deploy/webapps/clean'
set :branch, ENV['CIRCLE_BRANCH']
set :keep_releases, 4

set :log_level, :debug

set :ssh_options, {
  user: 'deploy'
}

set :tmp_dir, '/home/deploy/tmp'

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

  after :published, "ops:sync_gitfat_files"
  after :published, "ops:put_env"
  after :published, "composer:install"
  after :published, "laravel:permissions"
  after :published, "laravel:migrate"
  after :published, "laravel:optimize"
  # after :published, "laravel:cleanup"

  after :published, "ops:asset_compile"
  after :published, "ops:make_client_dirs"
  after :published, "ops:reset_app"
  #after :published, "ops:make_api_docs"
  after :published, "ops:asset_recompile"
  after :published, "laravel:restart_queue"

end
