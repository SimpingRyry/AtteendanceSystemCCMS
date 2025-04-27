// resources/js/components/Dashboard.jsx

import * as React from 'react';
import { BarChart, LineChart } from '@mui/x-charts';
import { DataGrid } from '@mui/x-data-grid';
import { Card, CardContent, Typography, Grid } from '@mui/material';

const payments = [
  { id: 1, name: 'Jane Doe', studentId: '2023001', org: 'Praxis', position: 'President', date: '2025-04-20' },
  { id: 2, name: 'John Smith', studentId: '2023012', org: 'ITS', position: 'Member', date: '2025-04-19' },
];

const Dashboard = () => {
  return (
    <div style={{ padding: 30, backgroundColor: '#f5f7fa', minHeight: '100vh' }}>
      <Typography variant="h4" align="center" gutterBottom fontWeight="bold" color="primary">
        D A S H B O A R D
      </Typography>

      {/* Overview Cards */}
      <Grid container spacing={3} mt={1}>
        {[
          { label: "Total Students", number: 1024, growth: "+4%", color: "#42a5f5" },
          { label: "Registered", number: 867, growth: "+6%", color: "#66bb6a" },
          { label: "Unregistered", number: 157, growth: "-2%", color: "#ef5350" },
          { label: "Total Fines", number: "₱12,450", growth: "+8%", color: "#ffa726" },
        ].map((item, index) => (
          <Grid item xs={12} md={3} key={index}>
            <Card sx={{ borderRadius: 5, backgroundColor: item.color, color: "#fff", boxShadow: 3 }}>
              <CardContent>
                <Typography variant="subtitle1">{item.label}</Typography>
                <Typography variant="h4" fontWeight="bold">{item.number}</Typography>
                <Typography color="inherit">
                  {item.growth}
                </Typography>
              </CardContent>
            </Card>
          </Grid>
        ))}
      </Grid>

      {/* Charts */}
      <Grid container spacing={3} mt={3}>
        <Grid item xs={12} md={6}>
          <Card sx={{ borderRadius: 5, boxShadow: 3 }}>
            <CardContent>
              <Typography variant="h6" gutterBottom fontWeight="bold">Absentees Report</Typography>
              <BarChart
                xAxis={[{ scaleType: 'band', data: ['Praxis', 'ITS'] }]}
                series={[
                  { data: [14, 23], color: '#42a5f5', label: 'Absentees' },
                ]}
                width={450}
                height={300}
              />
            </CardContent>
          </Card>
        </Grid>

        <Grid item xs={12} md={6}>
          <Card sx={{ borderRadius: 5, boxShadow: 3 }}>
            <CardContent>
              <Typography variant="h6" gutterBottom fontWeight="bold">Registration Trends</Typography>
              <LineChart
                xAxis={[{ scaleType: 'point', data: ['January', 'February', 'March', 'April'] }]}
                series={[
                  {
                    data: [200, 400, 600, 867],
                    color: '#66bb6a',
                    label: 'Registered Students',
                  },
                  {
                    data: [100, 130, 150, 157],
                    color: '#ef5350',
                    label: 'Unregistered Students',
                  }
                ]}
                width={450}
                height={300}
              />
            </CardContent>
          </Card>
        </Grid>
      </Grid>

      {/* Payments Table */}
      <Grid container spacing={3} mt={3}>
        <Grid item xs={12}>
          <Card sx={{ borderRadius: 5, boxShadow: 3 }}>
            <CardContent>
              <Typography variant="h6" gutterBottom fontWeight="bold">Payments</Typography>
              <div style={{ height: 300, width: '100%' }}>
                <DataGrid
                  rows={payments}
                  columns={[
                    { field: 'id', headerName: 'No.', width: 70 },
                    { field: 'name', headerName: 'Name', width: 180 },
                    { field: 'studentId', headerName: 'Student ID', width: 140 },
                    { field: 'org', headerName: 'Org', width: 120 },
                    { field: 'position', headerName: 'Position', width: 150 },
                    { field: 'date', headerName: 'Date', width: 150 },
                  ]}
                  pageSize={5}
                  rowsPerPageOptions={[5]}
                  sx={{
                    '& .MuiDataGrid-columnHeaders': {
                      backgroundColor: '#1976d2',
                      color: '#fff',
                      fontSize: 16,
                    },
                  }}
                />
              </div>
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </div>
  );
}

export default Dashboard;
