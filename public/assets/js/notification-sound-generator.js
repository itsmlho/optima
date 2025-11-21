/**
 * ============================================================================
 * NOTIFICATION SOUND GENERATOR
 * ============================================================================
 * Generate pleasant notification sound using Web Audio API
 * No need for external audio files
 * ============================================================================
 */

class NotificationSoundGenerator {
    constructor() {
        this.audioContext = null;
        this.initAudioContext();
    }
    
    initAudioContext() {
        try {
            // Create AudioContext (works in all modern browsers)
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            this.audioContext = new AudioContext();
        } catch (e) {
            console.warn('Web Audio API not supported');
        }
    }
    
    /**
     * Play a pleasant notification beep (like Facebook/Slack)
     */
    playNotificationBeep() {
        if (!this.audioContext) return;
        
        // Resume audio context if suspended (browser autoplay policy)
        if (this.audioContext.state === 'suspended') {
            this.audioContext.resume();
        }
        
        const now = this.audioContext.currentTime;
        
        // Create oscillator (tone generator)
        const oscillator = this.audioContext.createOscillator();
        const gainNode = this.audioContext.createGain();
        
        // Connect nodes
        oscillator.connect(gainNode);
        gainNode.connect(this.audioContext.destination);
        
        // Set frequency (pleasant notification tone)
        // Play two tones: C6 (1046.5 Hz) then E6 (1318.5 Hz)
        oscillator.frequency.setValueAtTime(1046.5, now); // C6
        oscillator.frequency.setValueAtTime(1318.5, now + 0.1); // E6 after 100ms
        
        // Set waveform (sine wave for pleasant sound)
        oscillator.type = 'sine';
        
        // Volume envelope (fade in and out)
        gainNode.gain.setValueAtTime(0, now);
        gainNode.gain.linearRampToValueAtTime(0.3, now + 0.05); // Fade in
        gainNode.gain.linearRampToValueAtTime(0.3, now + 0.15); // Hold
        gainNode.gain.linearRampToValueAtTime(0, now + 0.3); // Fade out
        
        // Play sound
        oscillator.start(now);
        oscillator.stop(now + 0.3); // Duration: 300ms
    }
    
    /**
     * Play critical alert sound (more urgent)
     */
    playCriticalAlert() {
        if (!this.audioContext) return;
        
        if (this.audioContext.state === 'suspended') {
            this.audioContext.resume();
        }
        
        const now = this.audioContext.currentTime;
        
        // Create multiple beeps for critical alert
        for (let i = 0; i < 3; i++) {
            const startTime = now + (i * 0.4);
            
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);
            
            // Higher frequency for urgency
            oscillator.frequency.setValueAtTime(1568, startTime); // G6
            oscillator.type = 'sine';
            
            // Quick beep
            gainNode.gain.setValueAtTime(0, startTime);
            gainNode.gain.linearRampToValueAtTime(0.4, startTime + 0.05);
            gainNode.gain.linearRampToValueAtTime(0, startTime + 0.15);
            
            oscillator.start(startTime);
            oscillator.stop(startTime + 0.15);
        }
    }
    
    /**
     * Play success sound (cheerful)
     */
    playSuccessSound() {
        if (!this.audioContext) return;
        
        if (this.audioContext.state === 'suspended') {
            this.audioContext.resume();
        }
        
        const now = this.audioContext.currentTime;
        const oscillator = this.audioContext.createOscillator();
        const gainNode = this.audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(this.audioContext.destination);
        
        // Play ascending tones (C-E-G)
        oscillator.frequency.setValueAtTime(523, now); // C5
        oscillator.frequency.setValueAtTime(659, now + 0.08); // E5
        oscillator.frequency.setValueAtTime(784, now + 0.16); // G5
        
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0, now);
        gainNode.gain.linearRampToValueAtTime(0.25, now + 0.05);
        gainNode.gain.linearRampToValueAtTime(0.25, now + 0.2);
        gainNode.gain.linearRampToValueAtTime(0, now + 0.35);
        
        oscillator.start(now);
        oscillator.stop(now + 0.35);
    }
}

// Export as global instance
window.NotificationSound = new NotificationSoundGenerator();

