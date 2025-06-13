
// lucidus-voice-proxy.js
const express = require('express');
const axios = require('axios');
const cors = require('cors');
const dotenv = require('dotenv');

dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware setup
app.use(cors());
app.use(express.json());

// Endpoint to fetch audio stream from ElevenLabs or other TTS services
app.post('/stream-audio', async (req, res) => {
    try {
        const { text, voiceId } = req.body;

        if (!text || !voiceId) {
            return res.status(400).send('Missing text or voiceId in request body');
        }

        // Request to ElevenLabs API for TTS
        const response = await axios.post(
            'https://api.elevenlabs.io/v1/speech/synthesize', 
            {
                text: text,
                voice_id: voiceId
            },
            {
                headers: {
                    'Authorization': `Bearer ${process.env.ELEVENLABS_API_KEY}`,
                    'Content-Type': 'application/json'
                },
                responseType: 'stream'
            }
        );

        res.setHeader('Content-Type', 'audio/mpeg');
        response.data.pipe(res);
    } catch (error) {
        console.error(error);
        res.status(500).send('Error generating audio');
    }
});

app.listen(PORT, () => {
    console.log(`Lucidus Voice Proxy listening on port ${PORT}`);
});
