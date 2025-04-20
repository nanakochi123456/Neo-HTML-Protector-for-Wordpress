: VBR2=96kbps by libfdk_aac

wsl ~/ffmpeg_build/bin/ffmpeg -y -i audiosrc/fastkakumei.wav -c:a libfdk_aac -vbr 2 audio/fastkakumei.m4a

wsl ~/ffmpeg_build/bin/ffmpeg -y -i audiosrc/fastkokken.wav -c:a libfdk_aac -vbr 2 audio/fastkokken.m4a

wsl ~/ffmpeg_build/bin/ffmpeg -y -i audiosrc/fasttokoroten.wav -c:a libfdk_aac -vbr 2 audio/fasttokoroten.m4a

wsl ~/ffmpeg_build/bin/ffmpeg -y -i audiosrc/msx_fanfa.wav -c:a libfdk_aac -vbr 2 audio/msx_fanfa.m4a

wsl ~/ffmpeg_build/bin/ffmpeg -y -i audiosrc/msx_open.wav -c:a libfdk_aac -vbr 2 audio/msx_open.m4a

wsl ~/ffmpeg_build/bin/ffmpeg -y -i audiosrc/sentou.wav -c:a libfdk_aac -vbr 2 audio/sentou.m4a

wsl ~/ffmpeg_build/bin/ffmpeg -y -i audiosrc/sm3_oyaji1.wav -c:a libfdk_aac -vbr 2 audio/sm3_oyaji1.m4a

wsl ~/ffmpeg_build/bin/ffmpeg -y -i audiosrc/sm3_oyaji2.wav -c:a libfdk_aac -vbr 2 audio/sm3_oyaji2.m4a

wsl ~/ffmpeg_build/bin/ffmpeg -y -i audiosrc/w-fanfa.wav -c:a libfdk_aac -vbr 2 audio/w-fanfa.m4a

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
