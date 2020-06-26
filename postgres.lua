-- load driver
local driver = require "luasql.postgres"
-- create environment object
env = assert (driver.postgres())
-- connect to data source
con = assert (env:connect("user=postgres password=postgres hostaddr=127.0.0.1"))

-- retrieve a cursor
cur = assert (con:execute"SELECT * from pg_catalog.pg_tables")
-- print all rows, the rows will be indexed by field names
row = cur:fetch ({}, "a")
while row do
  print(string.format("Name: %s, %s", row.tablename, row.tableowner))
  -- reusing the table of results
  row = cur:fetch (row, "a")
end
-- close everything
cur:close() -- already closed because all the result set was consumed
con:close()
env:close()




