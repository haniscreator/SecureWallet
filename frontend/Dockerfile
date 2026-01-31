# Stage 1: Build the Vue application
FROM node:20 as build-stage

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

# Build the app explicitly setting the API URL to localhost:8000/api as per requirement/convention for local docker usage
# If the user wants to access from host machine, localhost is correct.
# If the user wants container-to-container communication for SSR (not the case here usually), it would be distinct.
# Since it's an SPA, the browser (host) makes the requests. So localhost:8000 is likely correct from user's perspective.
RUN npm run build -- --mode=production

# Stage 2: Serve the application with Nginx
FROM nginx:alpine as production-stage

COPY --from=build-stage /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
