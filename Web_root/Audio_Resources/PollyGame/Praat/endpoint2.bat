:: Inputs:
:: %1 is the base directory where all scripts, praatcon.exe and m.wav are
:: %2 scrDirectory
:: %3 destDirectory
:: %4 fileName

:: Change directory to audio processing directory base
cd %1

:: Endpoint the original recording
praatcon.exe endpoint.praat %2 %2