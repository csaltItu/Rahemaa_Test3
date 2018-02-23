form M2F
	text srcfile s.wav
	text destfile mods.wav
endform

Read from file... 'srcfile$'
Extract left channel
sound_one$ = selected$ ("Sound")

# Change gender (make High) of the file and write back:
Change gender... 75.0 600.0 1.2 0.0 1.0 0.95
#Play
Write to WAV file... 'destfile$'

select all
Remove
