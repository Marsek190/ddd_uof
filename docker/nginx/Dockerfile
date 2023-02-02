FROM nginx:stable-alpine

ARG GID
ARG UID

#COPY ./conf.d/default.conf /etc/nginx/conf.d/
COPY ./nginx.conf /etc/nginx/nginx.conf

RUN addgroup -g ${GID} www && adduser -u ${UID} -G www -s /bin/sh -D www
