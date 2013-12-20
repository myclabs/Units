set :application, "Units"

set :stages,        %w(development testing production)
set :default_stage, "development"
set :stage_dir,     "app/config/deployment"
require 'capistrano/ext/multistage'

set :deploy_to,   "/home/web/units"
set :app_path,    "app"
ssh_options[:port] = "4269"
ssh_options[:forward_agent] = true
default_run_options[:pty] = true
set :use_sudo,    true

set :repository, "git@github.com:myclabs/Units.git"
set :scm,        :git
set :deploy_via, :remote_cache

set :use_composer,        true
set :model_manager,       "doctrine"
set :dump_assetic_assets, true
set :shared_files,        ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]

# Bugfix, see https://github.com/everzet/capifony/pull/205 and https://github.com/everzet/capifony/issues/404
before 'deploy:set_permissions', 'deploy:set_user'
namespace :deploy do
  desc "Set user to current user"
  task :set_user do
    set :user, capture("whoami")
    puts "current user: #{user}"
  end
end

set :writable_dirs,       ["app/cache", "app/logs"]
set :webserver_user,      "www-data"
set :permission_method,   :chown
set :use_set_permissions, true

set :keep_releases, 3
after "deploy", "deploy:cleanup"

logger.level = Logger::MAX_LEVEL
