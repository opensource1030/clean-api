# Load DSL and set up stages
require 'capistrano/setup'

# Include default deployment tasks
require 'capistrano/deploy'
require 'slackistrano/capistrano'
require_relative 'config/lib/custom_messaging'
require "capistrano/scm/git"
install_plugin Capistrano::SCM::Git

# 3rd Party Task --
require 'capistrano/file-permissions'

# Load custom tasks from `lib/capistrano/tasks' if you have any defined
Dir.glob('lib/capistrano/tasks/*.rake').each { |r| import r }
