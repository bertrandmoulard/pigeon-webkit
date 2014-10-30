require 'socket'

client = TCPSocket.open('localhost', 55830)
client.puts "Visit"
client.puts "1"
client.puts "16"
client.print "https://etsy.com"
client.puts "Body"
client.puts "0"
puts "output..."
puts client.gets
# response_length = client.gets.to_i
# puts client.read(response_length)
