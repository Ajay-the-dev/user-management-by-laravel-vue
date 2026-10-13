// Replaces resources/js/utils/echo.js in the demo build. There's no Reverb
// server, so channels live in memory and the mock API broadcasts into them.

const listeners = new Map()

const channel = (name) => ({
  listen(event, callback) {
    listeners.set(`${name}:${event}`, callback)
    return this
  },
})

export function emitBroadcast(channelName, event, message) {
  // Let the publishing request finish first, as a real socket push would
  setTimeout(() => listeners.get(`${channelName}:${event}`)?.({ message }), 600)
}

export const initEcho = async () => {
  window.Echo = {
    channel,
    private: channel,
    leave: (name) => {
      for (const key of listeners.keys()) if (key.startsWith(`${name}:`)) listeners.delete(key)
    },
    disconnect: () => {},
    connector: { pusher: { connection: { bind: () => {} } } },
  }
  return window.Echo
}
