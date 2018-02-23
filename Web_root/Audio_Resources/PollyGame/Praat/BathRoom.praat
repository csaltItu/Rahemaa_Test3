form BathRoom
	text srcfile s.wav
	text destfile mods.wav
endform

start_pitch = 100
end_pitch = 500

Read from file... 'srcfile$'
sound_one$ = selected$ ("Sound")

To Manipulation... 0.01 60 400

# Create a new pitch tier with the flat pitch:
select Sound 'sound_one$'
start = Get start time
end = Get end time
Create PitchTier... 'sound_one$' start end
Add point... start start_pitch
Add point... end end_pitch

# Combine and save the resulting file:
select Manipulation 'sound_one$'
plus PitchTier 'sound_one$'
Replace pitch tier
select Manipulation 'sound_one$'
Get resynthesis (PSOLA)

#Play
Write to WAV file... 'destfile$'

select all
Remove