form Whisper
	text srcfile s.wav
	text destfile mods.wav
endform

music$ = "m.wav"

Read from file... 'srcfile$'

#Extract left channel
sound_one$ = selected$ ("Sound")
Resample... 8000 50
duration = Get total duration
sound_two$ = selected$ ("Sound")
#Play
To LPC (burg)... 16 0.025 0.005 50.0
filter$ = selected$ ("LPC")

Create Sound from formula... "Noise" Mono 0.0 duration 8000 randomGauss(-0.2, 0.2)
Resample... 8000 50
Filter (pre-emphasis)... 50.0
noise$ = selected$ ("Sound")
#Play

select Sound 'sound_two$'
To Manipulation... 0.01 60 500
Extract pitch tier

# Three Options:
#To Sound (phonation)... 8000 1.0 0.05 0.7 0.03 3.0 4.0 no
To Sound (sine)... 8000
#To Sound (pulse train)... 8000 1.0 0.05 2000 no

plus Sound 'noise$'
Combine to stereo
Convert to mono
noise_pitch$ = selected$ ("Sound")

# With voicing or without:
#select Sound 'noise_pitch$'
select Sound 'noise$'

plus LPC 'filter$'
Filter... yes

#Play

# Save Simple Whisper
Write to WAV file... 'destfile$'

# Now Lengthen and Add music
#Lengthen (overlap-add)... 75.0 600 1.5 
#Shift times to... "start time" 5.0
#sound_two$ = selected$ ("Sound")
#duration = Get total duration
#Play

##Read from file... 'music$'
##Extract left channel
#sound_three$ = selected$ ("Sound")
#Resample... 8000 50
#duration = duration+10.0
#Extract part... 0 duration parabolic 1.0 no
#Multiply... 0.7
#sound_four$ = selected$ ("Sound")
#Play

#select Sound 'sound_two$'
#plus Sound 'sound_four$'

#Combine to stereo
#Convert to mono
#Play
#filenameFlat$ = "ModifiedRecordings\BKMUZWH"+name$
#Write to WAV file... 'filenameFlat$'

#select all
#Remove