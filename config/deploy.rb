set :use_sudo, :false

if ENV['environment'] == "production"
  set :application, "xerte"
  role :app, "172.20.1.63"
  role :web, "172.20.1.63"
  role :db,  "172.20.1.63", :primary => true
else
  set :application, "xerte_dev"
  role :app, "webdev.southdevon.ac.uk"
  role :web, "webdev.southdevon.ac.uk"
  role :db,  "webdev.southdevon.ac.uk", :primary => true
end

default_run_options[:pty] = true

set :repository,  "git@github.com:sdc/xerte.git"
set :branch,      "sdc_17"
set :deploy_to,   "/srv/#{application}"
set :scm, :git

namespace :deploy do
  %W(start stop restart migrate finalize_update).each do |event|
    task event do
      # don't
    end
  end
end

task :after_deploy do
  run "cp #{shared_path}/database.php #{current_path}/"
  run "chmod a+rwx #{current_path}"
  run "chmod a+rwx #{current_path}/error_logs"
  run "chmod a+rwx #{current_path}/import"

  run "rm -rf #{current_path}/USER-FILES"
  run "ln -s #{shared_path}/userfiles #{current_path}/USER-FILES"
  run "chmod a+rwx #{current_path}/USER-FILES"
end
