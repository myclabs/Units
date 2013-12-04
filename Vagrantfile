Vagrant.configure("2") do |config|

    config.vm.box = "precise32"
    config.vm.box_url = "http://files.vagrantup.com/precise32.box"

    $script = <<SCRIPT
# For PHP 5.5
apt-get update
apt-get install -y python-software-properties
add-apt-repository -y ppa:ondrej/php5
apt-get update

apt-get install -y curl git php5-curl php5-cli php-pear

echo 'date.timezone = "Europe/Paris"' > /etc/php5/cli/conf.d/mycsense.ini

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# PHPUnit
pear config-set auto_discover 1
pear install pear.phpunit.de/PHPUnit
SCRIPT

    config.vm.provision :shell, inline: $script

end
