:: Inputs:
:: %1 is the base directory where all scripts, praatcon.exe and m.wav are
:: %2 scrDirectory
:: %3 destDirectory
:: %4 fileName

:: Change directory to audio processing directory base
cd %1

:: Endpoint the original recording
praatcon.exe endpoint.praat %2%4 %2%4

:: Unmodified voice is set to effect no. 99
copy %2%4 %399-%4

:: Generate all voice mods. To change mod order, just change the order of processing here!
SET /A iter=0
praatcon.exe M2F.praat %2%4 %3%iter%-%4

SET /A iter+=1
praatcon.exe F2M.praat %2%4 %3%iter%-%4

SET /A iter+=1
praatcon.exe ChipMunk.praat %2%4 %3%iter%-%4

SET /A iter+=1
praatcon.exe BathRoom.praat %2%4 %3%iter%-%4

SET /A iter+=1
praatcon.exe fast_autotune_polly.praat %2%4 %3%iter%-%4

SET /A iter+=1
praatcon.exe Whisper.praat %2%4 %3%iter%-%4 

SET /A iter+=1
praatcon.exe BkMusic.praat %2%4 %3%iter%-%4