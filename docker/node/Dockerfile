FROM node:16-alpine
# RUN apk add --no-cache python2 g++ make # error on node:12-alpine
WORKDIR /var/www
COPY . .
RUN yarn install --production
#RUN npm install

# Run server
CMD ["node", "node_server/server.js"]
EXPOSE 3000
