role :app, %w{204.156.175.49}

set :stage, :develop
set :deploy_to, '/home/deploy/webapps/api'
set :branch, ENV['CIRCLE_BRANCH']
#set :keep_releases, 1

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
                execute "cd #{release_path} && composer install --no-scripts --prefer-source --optimize-autoloader"
            end
        end
    end

end

namespace :deploy do
  after :published, "ops:put_env"
  after :published, "composer:install"
  after :published, "laravel:permissions"
  after :published, "laravel:migrate"
  # after :published, "laravel:optimize"
  after :published, "ops:reset_app"
end
