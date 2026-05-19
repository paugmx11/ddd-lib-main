FROM alpine:3.20

WORKDIR /data

# Simple "data" container for SQLite persistence via a named volume mount.
VOLUME ["/data"]

CMD ["sh", "-lc", "echo \"BDD (sqlite) volume mounted at /data\"; tail -f /dev/null"]

