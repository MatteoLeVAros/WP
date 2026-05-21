FROM node:20

RUN apt-get update && apt-get install -y ca-certificates

COPY certs/zscaler-root.crt /usr/local/share/ca-certificates/zscaler-root.crt
RUN update-ca-certificates

ENV NODE_EXTRA_CA_CERTS=/usr/local/share/ca-certificates/zscaler-root.crt

RUN npm config set cafile /usr/local/share/ca-certificates/zscaler-root.crt

WORKDIR /app