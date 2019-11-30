if command -v python3 &>/dev/null; then
    echo Python 3 is installed
else
	echo Instalando Python
    sudo apt-get install python3.6
fi
pip install telepot
pip install requests