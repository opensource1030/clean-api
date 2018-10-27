role :app, %w{34.229.80.114}

set :stage, :production
set :deploy_to, '/home/forge/api.wirelessanalytics.com/'
set :branch, "env/prod"
set :keep_releases, 4
set :log_level, :debug

set :ssh_options, {
  user: 'forge'
}

set :tmp_dir, '/home/forge/api.wirelessanalytics.com/tmp'

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
    after :published, "ops:put_env"
    after :published, "composer:install"
    after :published, "laravel:permissions"
    #after :published, "laravel:migrate"
    after :published, "laravel:optimize"
end
