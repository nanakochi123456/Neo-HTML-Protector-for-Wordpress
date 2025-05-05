: VBR2=96kbps by libfdk_aac

: piano songs 48kbps 32000hz
wsl ffmpeg -y -i audiosrc/fastkakumei.wav -c:a aac -b:a 48k -ar 32000 audio/fastkakumei.m4a

wsl ffmpeg -y -i audiosrc/fastkokken.wav -c:a aac -b:a 48k -ar 32000 audio/fastkokken.m4a

wsl ffmpeg -y -i audiosrc/fasttokoroten.wav -c:a aac -b:a 48k -ar 32000 audio/fasttokoroten.m4a

: msx songs 32kbps 44100hz mono
wsl ffmpeg -y -i audiosrc/msx_fanfa.wav -c:a aac -b:a 32k -ar 44100 -ac 1 audio/msx_fanfa.m4a

wsl ffmpeg -y -i audiosrc/msx_open.wav -c:a aac -b:a 32k -ar 44100 -ac 1 audio/msx_open.m4a

: chatgpt made 256kbps 48000hz mono
wsl ffmpeg -y -i audiosrc/chatgpt.wav -c:a aac -b:a 256k -ar 48000 -ac 1 audio/chatgpt.m4a

wsl ffmpeg -y -i audiosrc/chatgpt2.wav -c:a aac -b:a 256k -ar 48000 -ac 1 audio/chatgpt2.m4a

wsl ffmpeg -y -i audiosrc/chatgpt3.wav -c:a aac -b:a 256k -ar 48000 -ac 1 audio/chatgpt3.m4a

: other songs 64kbps 44100hz
wsl ffmpeg -y -i audiosrc/sentou.wav -c:a aac -b:a 64k -ar 44100 audio/sentou.m4a

wsl ffmpeg -y -i audiosrc/sm3_oyaji1.wav -c:a aac -b:a 64k -ar 44100 audio/sm3_oyaji1.m4a

wsl ffmpeg -y -i audiosrc/sm3_oyaji2.wav -c:a aac -b:a 64k -ar 44100 audio/sm3_oyaji2.m4a

wsl ffmpeg -y -i audiosrc/w-fanfa.wav -c:a aac -b:a 64k -ar 44100 audio/w-fanfa.m4a

exit

:------------------------------------
:Build script

sudo apt update
sudo apt install -y autoconf automake build-essential cmake git libtool pkg-config texinfo zlib1g-dev \
  libass-dev libfreetype6-dev libsdl2-dev libtheora-dev libvorbis-dev libxcb1-dev libxcb-shm0-dev \
  libxcb-xfixes0-dev libvpx-dev libx264-dev libx265-dev libnuma-dev


cd ~
git clone --depth 1 https://github.com/mstorsjo/fdk-aac
cd fdk-aac
autoreconf -fiv
./configure --prefix="$HOME/ffmpeg_build" --disable-shared
make -j$(nproc)
make install


cd ~
git clone https://git.ffmpeg.org/ffmpeg.git ffmpeg
cd ffmpeg
PKG_CONFIG_PATH="$HOME/ffmpeg_build/lib/pkgconfig" ./configure \
  --prefix="$HOME/ffmpeg_build" \
  --pkg-config-flags="--static" \
  --extra-cflags="-I$HOME/ffmpeg_build/include" \
  --extra-ldflags="-L$HOME/ffmpeg_build/lib" \
  --extra-libs="-lpthread -lm" \
  --enable-gpl \
  --enable-nonfree \
  --enable-libfdk_aac \
  --disable-debug \
  --enable-static \
  --disable-shared
make -j$(nproc)
make install
