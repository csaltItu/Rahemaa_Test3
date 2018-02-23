form F2M
	text srcfile s.wav
	text destfile mods.wav
endform

Read from file... 'srcfile$'
Extract left channel
sound_one$ = selected$ ("Sound")

# Change gender (make Low) of the file and write back:
Change gender... 75.0 600.0 0.7 0.0 1.0 1.2
#Play
Write to WAV file... 'destfile$'

select all
Remove
