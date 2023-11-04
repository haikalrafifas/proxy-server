import http.server
import http.client

class ProxyHandler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        # Define the target host and port to which the proxy will forward requests
        target_host = 'example.com'  # Replace with the target host you want to proxy to
        target_port = 80  # Replace with the target port

        # Connect to the target server
        target_conn = http.client.HTTPConnection(target_host, target_port)
        target_conn.request(self.command, self.path, headers=dict(self.headers))

        # Get the response from the target server
        target_resp = target_conn.getresponse()

        # Send the response from the target server to the client
        self.send_response(target_resp.status)
        for header in target_resp.getheaders():
            self.send_header(*header)
        self.end_headers()
        self.wfile.write(target_resp.read())
        target_conn.close()

if __name__ == '__main__':
    server_address = ('', 8080)  # Replace with your desired host and port
    httpd = http.server.HTTPServer(server_address, ProxyHandler)
    print(f'Proxy server is running on port {server_address[1]}')
    httpd.serve_forever()
