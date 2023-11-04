# -*- mode: ruby -*-
# vi: set ft=ruby :

require 'json'
require 'yaml'

VAGRANTFILE_API_VERSION ||= "2"
confDir = $confDir ||= File.expand_path(File.dirname(__FILE__))

configYamlPath = confDir + "/Workspace.yaml"
configJsonPath = confDir + "/Workspace.json"
afterScriptPath = confDir + "/after.sh"
customizationScriptPath = confDir + "/user-customizations.sh"
aliasesPath = confDir + "/aliases"

require File.expand_path(File.dirname(__FILE__) + '/scripts/app.rb')

Vagrant.require_version '>= 2.2.4'

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    if File.exist? aliasesPath then
        config.vm.provision "file", source: aliasesPath, destination: "/tmp/bash_aliases"
        config.vm.provision "handle_aliases", type: "shell" do |s|
            s.inline = "awk '{ sub(\"\r$\", \"\"); print }' /tmp/bash_aliases > /home/vagrant/.bash_aliases && chown vagrant:vagrant /home/vagrant/.bash_aliases"
        end
    end

    if File.exist? configYamlPath then
        settings = YAML::load(File.read(configYamlPath))
    elsif File.exist? configJsonPath then
        settings = JSON::parse(File.read(configJsonPath))
    else
        abort "Workspace settings file not found in #{confDir}"
    end

    Workspace.configure(config, settings)

    if File.exist? afterScriptPath then
        config.vm.provision "Run after.sh", type: "shell", path: afterScriptPath, privileged: false, keep_color: true
    end

    if File.exist? customizationScriptPath then
        config.vm.provision "Run customize script", type: "shell", path: customizationScriptPath, privileged: false, keep_color: true
    end

    if Vagrant.has_plugin?('vagrant-hostsupdater')
        config.hostsupdater.remove_on_suspend = false
        config.hostsupdater.aliases = settings['sites'].map { |site| site['map'] }
    elsif Vagrant.has_plugin?('vagrant-hostmanager')
        config.hostmanager.enabled = true
        config.hostmanager.manage_host = true
        config.hostmanager.aliases = settings['sites'].map { |site| site['map'] }
    elsif Vagrant.has_plugin?('vagrant-goodhosts')
        config.goodhosts.aliases = settings['sites'].map { |site| site['map'] }
    end

    if Vagrant.has_plugin?('vagrant-notify-forwarder')
        config.notify_forwarder.enable = true
    end
end
