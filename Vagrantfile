# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/jammy64"
  config.vm.hostname = "etoa-gui"

  config.vm.network "private_network", ip: '192.168.33.11'

  if Vagrant::Util::Platform.windows? then
      config.vm.synced_folder './', '/var/www/etoa'
  else
      config.vm.synced_folder './', '/var/www/etoa', :nfs => true
  end



  config.vm.provider "virtualbox" do |vb|
    vb.customize [
      "modifyvm", :id,
      "--memory", "2048",
      "--cpus", "2"
    ]
  end

  config.vm.provision :shell, :inline => "apt-get update -y"
  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"
  config.vm.provision :shell, :path => "provision.sh"

end
